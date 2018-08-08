<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013-2015, Geert Bergman (geert@scrivo.nl), highlight.php
 * - 2014,      Daniel Lynge, highlight.php (contributor)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "highlight.js", "highlight.php", nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Highlight;

class Highlighter
{
    const SPAN_END_TAG = "</span>";

    private $options;

    private $modeBuffer = "";
    private $result = "";
    private $top = null;
    private $language = null;
    private $keywordCount = 0;
    private $relevance = 0;
    private $ignoreIllegals = false;

    private static $classMap = array();
    private static $languages = null;
    private static $aliases = null;

    private $autodetectSet = array(
        "xml", "json", "javascript", "css", "php", "http",
    );

    public function __construct()
    {
        $this->options = array(
            'classPrefix' => 'hljs-',
            'tabReplace' => null,
            'useBR' => false,
            'languages' => null,
        );

        self::registerLanguages();
    }

    private static function registerLanguages()
    {
        // Languages that take precedence in the classMap array.
        $languagePath = __DIR__ . DIRECTORY_SEPARATOR . "languages" . DIRECTORY_SEPARATOR;
        foreach (array("xml", "django", "javascript", "matlab", "cpp") as $languageId) {
            $filePath = $languagePath . $languageId . ".json";
            if (is_readable($filePath)) {
                self::registerLanguage($languageId, $filePath);
            }
        }

        $d = @dir($languagePath);
        if ($d) {
            while (($entry = $d->read()) !== false) {
                if (substr($entry, -5) === ".json") {
                    $languageId = substr($entry, 0, -5);
                    $filePath = $languagePath . $entry;
                    if (is_readable($filePath)) {
                        self::registerLanguage($languageId, $filePath);
                    }
                }
            }
            $d->close();
        }

        self::$languages = array_keys(self::$classMap);
    }

    /**
     * Register a language definition with the Highlighter's internal language
     * storage. Languages are stored in a static variable, so they'll be available
     * across all instances. You only need to register a language once.
     *
     * @param string $languageId The unique name of a language
     * @param string $filePath   The file path to the language definition
     * @param bool   $overwrite  Overwrite language if it already exists
     *
     * @return Language The object containing the definition for a language's markup
     */
    public static function registerLanguage($languageId, $filePath, $overwrite = false)
    {
        if (!isset(self::$classMap[$languageId]) || $overwrite) {
            $lang = new Language($languageId, $filePath);
            self::$classMap[$languageId] = $lang;

            if (isset($lang->mode->aliases)) {
                foreach ($lang->mode->aliases as $alias) {
                    self::$aliases[$alias] = $languageId;
                }
            }
        }

        return self::$classMap[$languageId];
    }

    private function testRe($re, $lexeme)
    {
        if (!$re) {
            return false;
        }
        $test = preg_match($re, $lexeme, $match, PREG_OFFSET_CAPTURE);
        if ($test === false) {
            throw new \Exception("Invalid regexp: " . var_export($re, true));
        }

        return count($match) && ($match[0][1] == 0);
    }

    private function subMode($lexeme, $mode)
    {
        for ($i = 0; $i < count($mode->contains); ++$i) {
            if ($this->testRe($mode->contains[$i]->beginRe, $lexeme)) {
                return $mode->contains[$i];
            }
        }
    }

    private function endOfMode($mode, $lexeme)
    {
        if ($this->testRe($mode->endRe, $lexeme)) {
            while ($mode->endsParent && $mode->parent) {
                $mode = $mode->parent;
            }

            return $mode;
        }
        if ($mode->endsWithParent) {
            return $this->endOfMode($mode->parent, $lexeme);
        }
    }

    private function isIllegal($lexeme, $mode)
    {
        return !$this->ignoreIllegals && $this->testRe($mode->illegalRe, $lexeme);
    }

    private function keywordMatch($mode, $match)
    {
        $kwd = $this->language->caseInsensitive ? mb_strtolower($match[0], "UTF-8") : $match[0];

        return isset($mode->keywords[$kwd]) ? $mode->keywords[$kwd] : null;
    }

    private function buildSpan($classname, $insideSpan, $leaveOpen = false, $noPrefix = false)
    {
        $classPrefix = $noPrefix ? "" : $this->options['classPrefix'];
        $openSpan = "<span class=\"" . $classPrefix;
        $closeSpan = $leaveOpen ? "" : self::SPAN_END_TAG;

        $openSpan .= $classname . "\">";

        return $openSpan . $insideSpan . $closeSpan;
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_NOQUOTES);
    }

    private function processKeywords()
    {
        if (empty($this->top->keywords)) {
            return $this->escape($this->modeBuffer);
        }

        $result = "";
        $lastIndex = 0;

        /* TODO: when using the crystal language file on django and twigs code
         * the values of $this->top->lexemesRe can become "" (empty). Check
         * if this behaviour is consistent with highlight.js.
         */
        if ($this->top->lexemesRe) {
            while (preg_match($this->top->lexemesRe, $this->modeBuffer, $match, PREG_OFFSET_CAPTURE, $lastIndex)) {
                $result .= $this->escape(substr($this->modeBuffer, $lastIndex, $match[0][1] - $lastIndex));
                $keyword_match = $this->keywordMatch($this->top, $match[0]);

                if ($keyword_match) {
                    $this->relevance += $keyword_match[1];
                    $result .= $this->buildSpan($keyword_match[0], $this->escape($match[0][0]));
                } else {
                    $result .= $this->escape($match[0][0]);
                }

                $lastIndex = strlen($match[0][0]) + $match[0][1];
            }
        }

        return $result . $this->escape(substr($this->modeBuffer, $lastIndex));
    }

    private function processSubLanguage()
    {
        try {
            $hl = new Highlighter();
            $hl->setAutodetectLanguages($this->autodetectSet);

            $explicit = is_string($this->top->subLanguage);
            if ($explicit && !in_array($this->top->subLanguage, self::$languages)) {
                return $this->escape($this->modeBuffer);
            }

            if ($explicit) {
                $res = $hl->highlight(
                    $this->top->subLanguage,
                    $this->modeBuffer,
                    true,
                    isset($this->continuations[$this->top->subLanguage]) ? $this->continuations[$this->top->subLanguage] : null
                );
            } else {
                $res = $hl->highlightAuto(
                    $this->modeBuffer,
                    count($this->top->subLanguage) ? $this->top->subLanguage : null
                );
            }
            // Counting embedded language score towards the host language may
            // be disabled with zeroing the containing mode relevance. Usecase
            // in point is Markdown that allows XML everywhere and makes every
            // XML snippet to have a much larger Markdown score.
            if ($this->top->relevance > 0) {
                $this->relevance += $res->relevance;
            }
            if ($explicit) {
                $this->continuations[$this->top->subLanguage] = $res->top;
            }

            return $this->buildSpan($res->language, $res->value, false, true);
        } catch (\Exception $e) {
            error_log("TODO, is this a relevant catch?");
            error_log($e);

            return $this->escape($this->modeBuffer);
        }
    }

    private function processBuffer()
    {
        $this->result .= $this->top->subLanguage ? $this->processSubLanguage() : $this->processKeywords();
        $this->modeBuffer = '';
    }

    private function startNewMode($mode)
    {
        $this->result .= $mode->className ? $this->buildSpan($mode->className, "", true) : "";

        $t = clone $mode;
        $t->parent = $this->top;
        $this->top = $t;
    }

    private function processLexeme($buffer, $lexeme = null)
    {
        $this->modeBuffer .= $buffer;

        if ($lexeme === null) {
            $this->processBuffer();

            return 0;
        }

        $new_mode = $this->subMode($lexeme, $this->top);
        if ($new_mode) {
            if ($new_mode->skip) {
                $this->modeBuffer .= $lexeme;
            } else {
                if ($new_mode->excludeBegin) {
                    $this->modeBuffer .= $lexeme;
                }
                $this->processBuffer();
                if (!$new_mode->returnBegin && !$new_mode->excludeBegin) {
                    $this->modeBuffer = $lexeme;
                }
            }
            $this->startNewMode($new_mode, $lexeme);

            return $new_mode->returnBegin ? 0 : strlen($lexeme);
        }

        $end_mode = $this->endOfMode($this->top, $lexeme);
        if ($end_mode) {
            $origin = $this->top;
            if ($origin->skip) {
                $this->modeBuffer .= $lexeme;
            } else {
                if (!($origin->returnEnd || $origin->excludeEnd)) {
                    $this->modeBuffer .= $lexeme;
                }
                $this->processBuffer();
                if ($origin->excludeEnd) {
                    $this->modeBuffer = $lexeme;
                }
            }
            do {
                if ($this->top->className) {
                    $this->result .= self::SPAN_END_TAG;
                }
                if (!$this->top->skip) {
                    $this->relevance += $this->top->relevance;
                }
                $this->top = $this->top->parent;
            } while ($this->top != $end_mode->parent);
            if ($end_mode->starts) {
                $this->startNewMode($end_mode->starts, "");
            }

            return $origin->returnEnd ? 0 : strlen($lexeme);
        }

        if ($this->isIllegal($lexeme, $this->top)) {
            $className = $this->top->className ? $this->top->className : "unnamed";
            $err = "Illegal lexeme \"{$lexeme}\" for mode \"{$className}\"";

            throw new \Exception($err);
        }

        // Parser should not reach this point as all types of lexemes should
        // be caught earlier, but if it does due to some bug make sure it
        // advances at least one character forward to prevent infinite looping.

        $this->modeBuffer .= $lexeme;
        $l = strlen($lexeme);

        return $l ? $l : 1;
    }

    /**
     * Replace tabs for something more usable.
     */
    private function replaceTabs($code)
    {
        if ($this->options['tabReplace'] !== null) {
            return str_replace("\t", $this->options['tabReplace'], $code);
        }

        return $code;
    }

    /**
     * Set the set of languages used for autodetection. When using
     * autodetection the code to highlight will be probed for every language
     * in this set. Limiting this set to only the languages you want to use
     * will greatly improve highlighting speed.
     *
     * @param array $set An array of language games to use for autodetection. This defaults
     *                   to a typical set Web development languages.
     */
    public function setAutodetectLanguages(array $set)
    {
        $this->autodetectSet = array_unique($set);
        self::registerLanguages();
    }

    /**
     * Get the tab replacement string.
     *
     * @return string The tab replacement string
     */
    public function getTabReplace()
    {
        return $this->options['tabReplace'];
    }

    /**
     * Set the tab replacement string. This defaults to NULL: no tabs
     * will be replaced.
     *
     * @param string $tabReplace The tab replacement string
     */
    public function setTabReplace($tabReplace)
    {
        $this->options['tabReplace'] = $tabReplace;
    }

    /**
     * Get the class prefix string.
     *
     * @return string
     *                The class prefix string
     */
    public function getClassPrefix()
    {
        return $this->options['classPrefix'];
    }

    /**
     * Set the class prefix string.
     *
     * @param string $classPrefix The class prefix string
     */
    public function setClassPrefix($classPrefix)
    {
        $this->options['classPrefix'] = $classPrefix;
    }

    /**
     * @throws \DomainException if the requested language was not in this
     *                          Highlighter's language set
     */
    private function getLanguage($name)
    {
        if (isset(self::$classMap[$name])) {
            return self::$classMap[$name];
        } elseif (isset(self::$aliases[$name]) && isset(self::$classMap[self::$aliases[$name]])) {
            return self::$classMap[self::$aliases[$name]];
        }

        throw new \DomainException("Unknown language: $name");
    }

    /**
     * Core highlighting function. Accepts a language name, or an alias, and a
     * string with the code to highlight. Returns an object with the following
     * properties:
     * - relevance (int)
     * - value (an HTML string with highlighting markup).
     *
     * @throws \DomainException if the requested language was not in this
     *                          Highlighter's language set
     * @throws \Exception       if an invalid regex was given in a language file
     */
    public function highlight($language, $code, $ignoreIllegals = true, $continuation = null)
    {
        $this->language = $this->getLanguage($language);
        $this->language->compile();
        $this->top = $continuation ? $continuation : $this->language->mode;
        $this->continuations = array();
        $this->result = "";

        for ($current = $this->top; $current != $this->language->mode; $current = $current->parent) {
            if ($current->className) {
                $this->result = $this->buildSpan($current->className, '', true) . $this->result;
            }
        }

        $this->modeBuffer = "";
        $this->relevance = 0;
        $this->ignoreIllegals = $ignoreIllegals;

        $res = new \stdClass();
        $res->relevance = 0;
        $res->value = "";
        $res->language = "";

        try {
            $match = null;
            $count = 0;
            $index = 0;

            while ($this->top && $this->top->terminators) {
                $test = preg_match($this->top->terminators, $code, $match, PREG_OFFSET_CAPTURE, $index);
                if ($test === false) {
                    throw new \Exception("Invalid regExp " . var_export($this->top->terminators, true));
                } elseif ($test === 0) {
                    break;
                }
                $count = $this->processLexeme(substr($code, $index, $match[0][1] - $index), $match[0][0]);
                $index = $match[0][1] + $count;
            }
            $this->processLexeme(substr($code, $index));

            for ($current = $this->top; isset($current->parent); $current = $current->parent) {
                if ($current->className) {
                    $this->result .= self::SPAN_END_TAG;
                }
            }

            $res->relevance = $this->relevance;
            $res->value = $this->replaceTabs($this->result);
            $res->language = $this->language->name;
            $res->top = $this->top;

            return $res;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "Illegal") !== false) {
                $res->value = $this->escape($code);

                return $res;
            }
            throw $e;
        }
    }

    /**
     * Highlight the given code by highlighting the given code with each
     * registered language and then finding the match with highest accuracy.
     *
     * @param string        $code
     * @param string[]|null $languageSubset When set to null, this method will
     *                                      attempt to highlight $code with each language (170+). Set this to
     *                                      an array of languages of your choice to limit the amount of languages
     *                                      to try.
     *
     * @throws \DomainException if the attempted language to check does not exist
     * @throws \Exception       if an invalid regex was given in a language file
     *
     * @return \stdClass
     */
    public function highlightAuto($code, $languageSubset = null)
    {
        $res = new \stdClass();
        $res->relevance = 0;
        $res->value = $this->escape($code);
        $res->language = "";
        $scnd = clone $res;

        $tmp = $languageSubset ? $languageSubset : $this->autodetectSet;

        foreach ($tmp as $l) {
            // don't fail if we run into a non-existent language
            try {
                $current = $this->highlight($l, $code, false);
            } catch (\DomainException $e) {
                continue;
            }

            if ($current->relevance > $scnd->relevance) {
                $scnd = $current;
            }
            if ($current->relevance > $res->relevance) {
                $scnd = $res;
                $res = $current;
            }
        }

        if ($scnd->language) {
            $res->secondBest = $scnd;
        }

        return $res;
    }

    /**
     * Return a list of all supported languages. Using this list in
     * setAutodetectLanguages will turn on autodetection for all supported
     * languages.
     *
     * @param bool $include_aliases specify whether language aliases
     *                              should be included as well
     *
     * @return string[] An array of language names
     */
    public function listLanguages($include_aliases = false)
    {
        if ($include_aliases === true) {
            return array_merge(self::$languages, array_keys(self::$aliases));
        }

        return self::$languages;
    }

    /**
     * Returns list of all available aliases for given language name.
     *
     * @param string $language name or alias of language to look-up
     *
     * @throws \DomainException if the requested language was not in this
     *                          Highlighter's language set
     *
     * @return string[] An array of all aliases associated with the requested
     *                  language name language. Passed-in name is included as
     *                  well.
     */
    public function getAliasesForLanguage($language)
    {
        $language = self::getLanguage($language);

        if ($language->aliases === null) {
            return array($language->name);
        }

        return array_merge(array($language->name), $language->aliases);
    }
}

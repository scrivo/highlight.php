<?php

/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniacsoftwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013-2019, Geert Bergman (geertscrivo.nl), highlight.php
 * - 2014       Daniel Lynge, highlight.php (contributor)
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

/**
 * @todo In highlight.php 10.x, replace the @final attribute with the `final` keyword.
 *
 * @final
 * @internal
 *
 * // Backward compatibility properties
 *
 * @property \stdClass $mode (DEPRECATED) All properties traditionally inside of $mode are now available directly from this class.
 * @property bool $caseInsensitive (DEPRECATED) Due to compatibility requirements with highlight.js, use `case_insensitive` instead.
 *
 * // Language definition set via language definition JSON files
 *
 * @property bool $case_insensitive
 * @property array $aliases
 * @property string $className
 * @property string $begin
 * @property RegEx $beginRe
 * @property string $end
 * @property RegEx $endRe
 * @property string $beginKeywords
 * @property bool $endsWithParent
 * @property bool $endsParent
 * @property bool $endSameAsBegin
 * @property string $lexemes
 * @property RegEx $lexemesRe
 * @property array<string, array> $keywords
 * @property string $illegal
 * @property RegEx $illegalRe
 * @property bool $excludeBegin
 * @property bool $excludeEnd
 * @property bool $returnBegin
 * @property bool $returnEnd
 * @property array $contains
 * @property string $starts
 * @property array $variants
 * @property string|array $subLanguage
 * @property bool $skip
 * @property bool $disableAutodetect
 *
 * // Properties set at runtime by the language compilation process
 *
 * @property string $terminators
 * @property string $terminator_end
 * @property bool $compiled
 * @property int $relevance
 *
 * @see https://highlightjs.readthedocs.io/en/latest/reference.html
 */
class Language
{
    private static $COMMON_KEYWORDS = ['of', 'and', 'for', 'in', 'not', 'or', 'if', 'then'];

    /**
     * @var string
     */
    public $name;

    /**
     * @var \stdClass|null
     */
    private $mode = null;

    /**
     * @todo Remove in highlight.php 10.x
     *
     * @deprecated 9.16.0 This method is no longer used since all of these properties will be set through magic methods instead
     *
     * @param \stdClass|null $e
     */
    public function complete(&$e)
    {
        if (!isset($e)) {
            $e = new \stdClass();
        }

        $patch = array(
            "begin" => true,
            "end" => true,
            "lexemes" => true,
            "illegal" => true,
        );

        $def = array(
            "begin" => "",
            "beginRe" => "",
            "beginKeywords" => "",
            "excludeBegin" => "",
            "returnBegin" => "",
            "end" => "",
            "endRe" => "",
            "endSameAsBegin" => "",
            "endsParent" => "",
            "endsWithParent" => "",
            "excludeEnd" => "",
            "returnEnd" => "",
            "starts" => "",
            "terminators" => "",
            "terminatorEnd" => "",
            "lexemes" => "",
            "lexemesRe" => "",
            "illegal" => "",
            "illegalRe" => "",
            "className" => "",
            "contains" => array(),
            "keywords" => null,
            "subLanguage" => null,
            "subLanguageMode" => "",
            "compiled" => false,
            "relevance" => 1,
            "skip" => false,
        );

        foreach ($patch as $k => $v) {
            if (isset($e->$k)) {
                $e->$k = str_replace("\\/", "/", $e->$k);
                $e->$k = str_replace("/", "\\/", $e->$k);
            }
        }

        foreach ($def as $k => $v) {
            if (!isset($e->$k) && is_object($e)) {
                $e->$k = $v;
            }
        }
    }

    public function __construct($lang, $filePath)
    {
        $json = file_get_contents($filePath);
        $this->mode = json_decode($json);
        $this->name = $lang;
    }

    public function __get($name)
    {
        if ($name === 'mode') {
            @trigger_error('The "mode" property will be removed in highlight.php 10.x', E_USER_DEPRECATED);

            return $this->mode;
        }

        if ($name === 'caseInsensitive') {
            @trigger_error('Due to compatibility requirements with highlight.js, use "case_insensitive" instead.', E_USER_DEPRECATED);

            return $this->mode->case_insensitive;
        }

        if (isset($this->mode->{$name})) {
            $patch = array(
                "begin" => true,
                "end" => true,
                "lexemes" => true,
                "illegal" => true,
            );

            $value = $this->mode->{$name};

            if (isset($patch[$name])) {
                $value = str_replace("\\/", "/", $value);
                $value = str_replace("/", "\\/", $value);
            }

            $this->{$name} = $value;

            return $this->{$name};
        }

        return null;
    }

    /**
     * @param string $value
     * @param bool $global
     *
     * @return RegEx
     */
    private function langRe($value, $global = false)
    {
        // PCRE allows us to change the definition of "new line." The
        // `(*ANYCRLF)` matches `\r`, `\n`, and `\r\n` for `$`
        //
        //   https://www.pcre.org/original/doc/html/pcrepattern.html

        $regex = "/(*ANYCRLF){$value}/um" . ($this->case_insensitive ? "i" : "");

        return new RegEx($regex);
    }

    /**
     * @param RegEx|string $re
     *
     * @return int
     */
    private function reCountMatchGroups($re)
    {
        $results = [];
        preg_match_all('#' . (string)$re . '|#', '', $results);

        return count($results) - 1;
    }

    private function processKeyWords($kw)
    {
        if (is_string($kw)) {
            if ($this->caseInsensitive) {
                $kw = mb_strtolower($kw, "UTF-8");
            }
            $kw = array("keyword" => explode(" ", $kw));
        } else {
            foreach ($kw as $cls => $vl) {
                if (!is_array($vl)) {
                    if ($this->caseInsensitive) {
                        $vl = mb_strtolower($vl, "UTF-8");
                    }
                    $kw->$cls = explode(" ", $vl);
                }
            }
        }

        return $kw;
    }

    private function inherit()
    {
        $result = new \stdClass();
        $objects = func_get_args();
        $parent = array_shift($objects);

        foreach ($parent as $key => $value) {
            $result->{$key} = $value;
        }

        foreach ($objects as $object) {
            foreach ($object as $key => $value) {
                $result->{$key} = $value;
            }
        }

        return $result;
    }

    private function dependencyOnParent($mode)
    {
        if (!$mode) {
            return false;
        }

        if (isset($mode->endsWithParent) && $mode->endsWithParent) {
            return $mode->endsWithParent;
        }

        return $this->dependencyOnParent(isset($mode->starts) ? $mode->starts : null);
    }

    private function expandMode($mode)
    {
        if (isset($mode->variants) && !isset($mode->cachedVariants)) {
            $mode->cachedVariants = array();

            foreach ($mode->variants as $variant) {
                $mode->cachedVariants[] = $this->inherit($mode, array('variants' => null), $variant);
            }
        }

        // EXPAND
        // if we have variants then essentually "replace" the mode with the variants
        // this happens in compileMode, where this function is called from
        if (isset($mode->cachedVariants)) {
            return $mode->cachedVariants;
        }

        // CLONE
        // if we have dependencies on parents then we need a unique
        // instance of ourselves, so we can be reused with many
        // different parents without issue
        if ($this->dependencyOnParent($mode)) {
            return array($this->inherit($mode, [
                'starts' => isset($mode->starts) && $mode->starts ? $this->inherit($mode->starts) : null
            ]));
        }

        // no special dependency issues, just return ourselves
        return array($mode);
    }

    /**
     * joinRe logically computes regexps.join(separator), but fixes the
     * backreferences so they continue to match.
     *
     * it also places each individual regular expression into it's own
     * match group, keeping track of the sequencing of those match groups
     * is currently an exercise for the caller. :-)
     *
     * @param array  $regexps
     * @param string $separator
     *
     * @return string
     */
    private function joinRe($regexps, $separator)
    {
        // backreferenceRe matches an open parenthesis or backreference. To avoid
        // an incorrect parse, it additionally matches the following:
        // - [...] elements, where the meaning of parentheses and escapes change
        // - other escape sequences, so we do not misparse escape sequences as
        //   interesting elements
        // - non-matching or lookahead parentheses, which do not capture. These
        //   follow the '(' with a '?'.
        $backreferenceRe = '#\[(?:[^\\\\\]]|\\\.)*\]|\(\??|\\\([1-9][0-9]*)|\\\.#';
        $numCaptures = 0;
        $ret = '';

        $strLen = count($regexps);
        for ($i = 0; $i < $strLen; ++$i) {
            $numCaptures += 1;
            $offset = $numCaptures;
            $re = $regexps[$i];

            if ($i > 0) {
                $ret .= $separator;
            }

            $ret .= "(";

            while (strlen($re) > 0) {
                $matches = array();
                $matchFound = preg_match($backreferenceRe, $re, $matches, PREG_OFFSET_CAPTURE);

                if ($matchFound === 0) {
                    $ret .= $re;
                    break;
                }

                // PHP aliases to match the JS naming conventions
                $match = $matches[0];
                $index = $match[1];

                $ret .= substr($re, 0, $index);
                $re = substr($re, $index + strlen($match[0]));

                if (substr($match[0], 0, 1) === '\\' && isset($matches[1])) {
                    // Adjust the backreference.
                    $ret .= "\\" . strval(intval($matches[1][0]) + $offset);
                } else {
                    $ret .= $match[0];
                    if ($match[0] == "(") {
                        ++$numCaptures;
                    }
                }
            }

            $ret .= ")";
        }

        return $ret;
    }

    private function buildModeRegex($mode)
    {
        $matchIndexes = [];
        $matcherRe = null;
        $regexes = [];
        $matcher = new \stdClass();
        $matchAt = 1;

        $addRule = function ($rule, $regex) use (&$matchIndexes, &$matchAt, &$regexes) {
            $matchIndexes[$matchAt] = $rule;
            $regexes[] = [$rule, $regex];
            $matchAt += $this->reCountMatchGroups($regex) + 1;
        };

        $term = null;
        for ($i = 0; $i < count($mode->contains); $i++) {
            $re = null;
            $term = $mode->contains[$i];

            if ($term->beginKeywords) {
                $re = "\.?(?:" . $term->begin . ")\.?";
            } else {
                $re = $term->begin;
            }

            $addRule($term, $re);
        }
        if ($mode->terminatorEnd) {
            $addRule('end', $mode->terminatorEnd);
        }
        if ($mode->illegal) {
            $addRule('illegal', $mode->illegal);
        }

        $terminators = [];
        foreach ($regexes as $regex) {
            $terminators[] = $regex[1];
        }
        $matcherRe = $this->langRe($this->joinRe($terminators, '|'), true);

        $matcher->lastIndex = 0;
        $matcher->exec = function ($s) use ($regexes, $matcher, $matcherRe, $matchIndexes, $mode) {
            if (count($regexes) === 0) {
                return null;
            }

            $matcherRe->lastIndex = $matcher->lastIndex;
            $match = $matcherRe->exec($s);
            if (!$match) {
                return null;
            }

            $rule = null;
            for ($i = 0; $i < count($match); $i ++) {
                if ($match[$i] && isset($matchIndexes[$i]) && $matchIndexes[$i]) {
                    $rule = $matchIndexes[$i];
                    break;
                }
            }

            if (is_string($rule)) {
                $match->type = $rule;
                $match->extra = [$mode->illegal, $mode->terminatorEnd];
            } else {
                $match->type = "begin";
                $match->rule = $rule;
            }

            return $match;
        };

        return $matcher;
    }

    private function compileMode($mode, $parent = null)
    {
        if (isset($mode->compiled)) {
            return;
        }
        $this->complete($mode);
        $mode->compiled = true;

        $mode->keywords = $mode->keywords ? $mode->keywords : $mode->beginKeywords;

        /* Note: JsonRef method creates different references as those in the
         * original source files. Two modes may refer to the same keywords
         * set, so only testing if the mode has keywords is not enough: the
         * mode's keywords might be compiled already, so it is necessary
         * to do an 'is_array' check.
         */
        if ($mode->keywords && !is_array($mode->keywords)) {
            $compiledKeywords = array();

            $mode->lexemesRe = $this->langRe($mode->lexemes ? $mode->lexemes : "\w+", true);

            foreach ($this->processKeyWords($mode->keywords) as $clsNm => $dat) {
                if (!is_array($dat)) {
                    $dat = array($dat);
                }
                foreach ($dat as $kw) {
                    $pair = explode("|", $kw);
                    $compiledKeywords[$pair[0]] = array($clsNm, isset($pair[1]) ? intval($pair[1]) : 1);
                }
            }
            $mode->keywords = $compiledKeywords;
        }

        if ($parent) {
            if ($mode->beginKeywords) {
                $mode->begin = "\\b(" . implode("|", explode(" ", $mode->beginKeywords)) . ")\\b";
            }
            if (!$mode->begin) {
                $mode->begin = "\B|\b";
            }
            $mode->beginRe = $this->langRe($mode->begin);
            if ($mode->endSameAsBegin) {
                $mode->end = $mode->begin;
            }
            if (!$mode->end && !$mode->endsWithParent) {
                $mode->end = "\B|\b";
            }
            if ($mode->end) {
                $mode->endRe = $this->langRe($mode->end);
            }
            $mode->terminatorEnd = $mode->end;
            if ($mode->endsWithParent && $parent->terminatorEnd) {
                $mode->terminatorEnd .= ($mode->end ? "|" : "") . $parent->terminatorEnd;
            }
        }

        if ($mode->illegal) {
            $mode->illegalRe = $this->langRe($mode->illegal);
        }

        $expandedContains = array();
        foreach ($mode->contains as $c) {
            $expandedContains = array_merge($expandedContains, $this->expandMode(
                $c === 'self' ? $mode : $c
            ));
        }

        $mode->contains = $expandedContains;

        for ($i = 0; $i < count($mode->contains); ++$i) {
            $this->compileMode($mode->contains[$i], $mode);
        }

        if ($mode->starts) {
            $this->compileMode($mode->starts, $parent);
        }

        $mode->terminators = $this->buildModeRegex($mode);
    }

    private function compileKeywords($rawKeywords, $caseSensitive)
    {
        $compiledKeywords = [];

        $splitAndCompile = function ($className, $str) use ($compiledKeywords, $caseSensitive) {
            if ($caseSensitive) {
                $str = strtolower($str);
            }

            $keywords = explode(' ', $str);

            foreach ($keywords as $keyword) {
                $pair = explode('|', $keyword);
                $compiledKeywords[$pair[0]] = [$className, $this->scoreForKeyword($pair[0], $pair[1])];
            }
        };

        if (is_string($rawKeywords)) {
            $splitAndCompile("keyword", $rawKeywords);
        } else {
            foreach ($rawKeywords as $className => $rawKeyword) {
                $splitAndCompile($className, $rawKeyword);
            }
        }
    }

    private function scoreForKeyword($keyword, $providedScore)
    {
        if ($providedScore) {
            return (int)$providedScore;
        }

        return $this->commonKeyword($keyword) ? 0 : 1;
    }

    private function commonKeyword($word)
    {
        return in_array(strtolower($word), self::$COMMON_KEYWORDS);
    }

    public function compile()
    {
        if (!$this->mode->compiled) {
            $jr = new JsonRef();
            $this->mode = $jr->decode($this->mode);
            $this->compileMode($this);
        }
    }
}

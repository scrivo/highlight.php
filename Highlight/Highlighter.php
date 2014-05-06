<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013-2014, Geert Bergman (geert@scrivo.nl), highlight.php
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
    private $modeBuffer = "";
    private $result = "";
    private $top = null;
    private $language = null;
    private $keywordCount = 0;
    private $relevance = 0;
    private $ignoreIllegals = false;
    
    private static $classMap = array();
    
    private $options = null;
        
    private $autodetectSet = array(
        "html", "xml", "json", "javascript", "css", "php", "http"
    );
    
    public function __construct()
    {
        $this->options = (object)array(
            "classPrefix" => "hljs-",
            "tabReplace" => null,
            "useBR" => false,
            "languages" => null
        );
    }
    
    private function createLanguage($languageId) 
    {
        if (!isset(self::$classMap[$languageId])) {
            self::$classMap[$languageId] = new Language($languageId);
        }
        return self::$classMap[$languageId];
    }

    public function testRe($re, $lexeme) 
    {
        if (!$re) {
            return false;
        }
        preg_match($re, $lexeme, $match, PREG_OFFSET_CAPTURE);
        return count($match) && ($match[0][1] == 0);
    }
    
    private function subMode($lexeme, $mode) 
    {
        for ($i=0; $i<count($mode->contains); $i++) {
            if ($this->testRe($mode->contains[$i]->beginRe, $lexeme)) {
                return $mode->contains[$i];
            }
        }
    }

    private function endOfMode($mode, $lexeme) 
    {
        if ($this->testRe($mode->endRe, $lexeme)) {
            return $mode;
        }
        if ($mode->endsWithParent) {
            return $this->endOfMode($mode->parent, $lexeme);
        }
    }

    private function isIllegal($lexeme, $mode) 
    {
        return 
            !$this->ignoreIllegals && $this->testRe($mode->illegalRe, $lexeme);
    }

    private function keywordMatch($mode, $match) 
    {
        $kwd = $this->language->caseInsensitive 
            ? mb_strtolower($match[0], "UTF-8") : $match[0];

        return isset($mode->keywords[$kwd]) ? $mode->keywords[$kwd] : null;
    }

    private function buildSpan(
            $classname, $insideSpan, $leaveOpen=false, $noPrefix=false) 
    {
        $classPrefix = $noPrefix ? "" : $this->options->classPrefix;
        $openSpan = "<span class=\"" . $classPrefix;
        $closeSpan = $leaveOpen ? "" : "</span>";
    
        $openSpan .= $classname . "\">";
    
        return $openSpan . $insideSpan . $closeSpan;
    }
    
    private function processKeywords() 
    {
        $buffer = htmlspecialchars($this->modeBuffer, ENT_NOQUOTES);
        if (!$this->top->keywords) {
            return $buffer;
        }
                
        $result = "";
        $lastIndex = 0;

        while (preg_match($this->top->lexemesRe, $buffer, $match, 
                PREG_OFFSET_CAPTURE, $lastIndex)) {

            $result .= substr($buffer, $lastIndex, $match[0][1] - $lastIndex);
            $keyword_match = $this->keywordMatch($this->top, $match[0]);

            if ($keyword_match) {
                $this->relevance += $keyword_match[1];
                $result .= $this->buildSpan($keyword_match[0], $match[0][0]);
            } else {
                $result .= $match[0][0];
            }

            $lastIndex = strlen($match[0][0]) + $match[0][1];
        }

        return $result . substr($buffer, $lastIndex);
    }

    private function processSubLanguage() 
    {
        try {
            $hl = new Highlighter();
            $hl->autodetectSet = $this->autodetectSet;

            $slm = isset($this->top->subLanguageMode) ? 
                $this->top->subLanguageMode : null;

            if ($this->top->subLanguage) {
                $res = $hl->highlight($this->top->subLanguage, 
                    $this->modeBuffer, true, $this->top->continuation->top);
            } else {
                $res = $hl->highlightAuto($this->modeBuffer);
            }
            // Counting embedded language score towards the host language may 
            // be disabled with zeroing the containing mode relevance. Usecase 
            // in point is Markdown that allows XML everywhere and makes every 
            // XML snippet to have a much larger Markdown score.
            if ($this->top->relevance > 0) {
                $this->relevance += $res->relevance;
            }
            if ($this->top->subLanguageMode == "continuous") {
                $this->top->continuation->top = $res->top;
            }            
            return $this->buildSpan($res->language, $res->value, false, true);
                
        } catch (\Exception $e) {
            return htmlspecialchars($this->modeBuffer, ENT_NOQUOTES);
        }
    }

    private function processBuffer() 
    {
        return $this->top->subLanguage!==null 
            ? $this->processSubLanguage() : $this->processKeywords();
    }

    private function startNewMode($mode, $lexeme) 
    {
        $markup = $mode->className 
            ? $this->buildSpan($mode->className, "", true) : "";

        if ($mode->returnBegin) {
            $this->result .= $markup;
            $this->modeBuffer = "";
        } elseif ($mode->excludeBegin) {
            $this->result .= htmlspecialchars($lexeme, ENT_NOQUOTES) . $markup;
            $this->modeBuffer = "";
        } else {
            $this->result .= $markup;
            $this->modeBuffer = $lexeme;
        }

        $t = clone $mode;
        $t->parent = $this->top;
        $this->top = $t;
    }

    private function processLexeme($buffer, $lexeme=null) 
    {
        $this->modeBuffer .= $buffer;
        
        if (null === $lexeme) {
            $this->result .= $this->processBuffer();
            return 0;
        }

        $new_mode = $this->subMode($lexeme, $this->top);
        if ($new_mode) {
            $this->result .= $this->processBuffer();
            $this->startNewMode($new_mode, $lexeme);
            return $new_mode->returnBegin ? 0 : strlen($lexeme);
        }

        $end_mode = $this->endOfMode($this->top, $lexeme);
        if ($end_mode) {
            $origin = $this->top;
            if (!($origin->returnEnd || $origin->excludeEnd)) {
                $this->modeBuffer .= $lexeme;
            }
            $this->result .= $this->processBuffer();
            do {
                if ($this->top->className) {
                    $this->result .= "</span>";
                }
                $this->relevance += $this->top->relevance;
                $this->top = $this->top->parent;
            } while ($this->top != $end_mode->parent);
            if ($origin->excludeEnd) {
                $this->result .= htmlspecialchars($lexeme, ENT_NOQUOTES);
            }
            $this->modeBuffer = "";
            if ($end_mode->starts) {
                $this->startNewMode($end_mode->starts, "");
            }
            return $origin->returnEnd ? 0 : strlen($lexeme);
        }

        if ($this->isIllegal($lexeme, $this->top)) {
            $className = $this->top->className 
                ? $this->top->className : "unnamed";
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
    
    public function setAutodetectLanguages(array $set) 
    {
        $this->autodetectSet = array_unique($set);
    }

    /**
     * Core highlighting function. Accepts a language name, or an alias, and a
     * string with the code to highlight. Returns an object with the following
     * properties:
     * - relevance (int)
     * - value (an HTML string with highlighting markup)
     */    
    public function highlight(
            $language, $code, $ignoreIllegals=true, $continuation=null) 
    {
        $this->language = $this->createLanguage($language);
        $this->top = $continuation ? $continuation : $this->language->mode;
        $this->result = "";

        for ($current = $this->top; $current != $this->language->mode; 
                $current = $current->parent) {
            if ($current->className) {
                $this->result = 
                    $this->buildSpan($current->className, $this->result, true);
            }
        }
        
        $this->modeBuffer = "";
        $this->relevance = 0;
        $this->ignoreIllegals = $ignoreIllegals;
        
        $res = new \stdClass;
        $res->relevance = 0;
        $res->value = "";
        $res->language = "";

        try {
            $match = null;
            $count = 0;
            $index = 0;

            while ($this->top->terminators) {
                if (!preg_match($this->top->terminators, $code, $match, 
                        PREG_OFFSET_CAPTURE, $index)) {
                    break;
                }
                $count = $this->processLexeme(
                    substr($code, $index, $match[0][1] - $index), $match[0][0]);
                $index = $match[0][1] + $count;
            }
            $this->processLexeme(substr($code, $index));

            for ($current = $this->top; $current != $this->language->mode; 
                    $current = $current->parent) {
                if ($current->className) {
                    $this->result .= "</span>";
                }
            }
                
            $res->relevance = $this->relevance;
            $res->value = $this->result;
            $res->language = $this->language->name;
            $res->top = $this->top;
            
            return $res;
            
        } catch (\Exception $e) {
            
            if (strpos($e->getMessage(), "Illegal") !== false) {
                $res->value = htmlspecialchars($code, ENT_NOQUOTES);
                return $res;
            } else {
                throw $e;
            }
        }
    }
    
    public function highlightAuto($code) 
    {
        $res = new \stdClass;
        $res->relevance = 0;
        $res->value = htmlspecialchars($code);
        $res->language = "";
        $scnd = clone $res;
        
        foreach ($this->autodetectSet as $l) {
            $current = $this->highlight($l, $code, false);
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
}

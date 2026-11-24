<?php

/* Copyright (c) 2019 Geert Bergman (geert@scrivo.nl), highlight.php
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
 * @internal
 *
 * @since 9.16.0.0
 */
abstract class RegExUtils
{
    /**
     * @param string $value
     * @param bool   $global
     * @param bool   $case_insensitive
     *
     * @return RegEx
     */
    public static function langRe($value, $global, $case_insensitive)
    {
        // PCRE allows us to change the definition of "new line." The
        // `(*ANYCRLF)` matches `\r`, `\n`, and `\r\n` for `$`
        //
        //   https://www.pcre.org/original/doc/html/pcrepattern.html

        // PCRE requires us to tell it the string can be UTF-8, so the 'u' modifier
        // is required. The `u` flag for PCRE is different from JS' unicode flag.

        $regex = "/(*ANYCRLF)" . self::jsToPcre($value) . "/um" . ($case_insensitive ? "i" : "");

        return new RegEx($regex);
    }

    /** JavaScript's \w is ASCII only, PCRE's \w (with the u modifier) is not */
    public const WORD = 'a-zA-Z0-9_';

    /**
     * Translates the source of a JavaScript regular expression into an equivalent
     * PCRE pattern that can be used between `/` delimiters.
     *
     * Language definitions use JavaScript regex syntax, these are the differences
     * that matter:
     *
     * - `/` must be escaped because it is our delimiter;
     * - `\uXXXX` and `\u{X...}` become `\x{XXXX}`;
     * - `\w`, `\d` and `\b` only consider ASCII characters in JavaScript, while
     *   PHP makes them Unicode aware when the `u` modifier is used;
     * - `[]` (never matches) and `[^]` (matches anything) have no PCRE equivalent;
     * - inside a character class an unescaped `[` is a literal, and a `-` next to
     *   a class escape such as `\w` is a literal;
     * - escaped letters that are not JS escapes are literals in JS but errors
     *   (or something else, like `\e`, `\h`, `\v`) in PCRE.
     *
     * @since 10.0.0
     *
     * @param string $source
     *
     * @return string
     */
    public static function jsToPcre($source)
    {
        static $cache = array();

        if (isset($cache[$source])) {
            return $cache[$source];
        }

        $out = '';
        $length = strlen($source);

        for ($i = 0; $i < $length; ++$i) {
            $ch = $source[$i];

            if ($ch === '\\') {
                $escape = self::translateEscape($source, $i, false);
                $out .= $escape[0];
                $i = $escape[1];
            } elseif ($ch === '[') {
                $class = self::translateClass($source, $i);
                $out .= $class[0];
                $i = $class[1];
            } else {
                $out .= $ch === '/' ? '\/' : $ch;
            }
        }

        return $cache[$source] = $out;
    }

    /**
     * @param string $source
     * @param int    $i      position of the opening `[`
     *
     * @return array{0: string, 1: int} the PCRE equivalent and the position of the closing `]`
     */
    private static function translateClass($source, $i)
    {
        $length = strlen($source);
        $start = $i;
        $negated = isset($source[$i + 1]) && $source[$i + 1] === '^';
        $i += $negated ? 2 : 1;

        $items = '';
        // \W and \D can't be combined with other items in a PCRE class when \w and \d are restricted to ASCII
        $complements = array();
        $prevClassEscape = false;

        for (; $i < $length; ++$i) {
            $ch = $source[$i];

            if ($ch === ']') {
                break;
            }

            if ($ch === '\\') {
                $next = isset($source[$i + 1]) ? $source[$i + 1] : '';
                if ($next === 'W' || $next === 'D') {
                    $complements[] = $next === 'W' ? self::WORD : '0-9';
                    $prevClassEscape = true;
                    ++$i;

                    continue;
                }
                $escape = self::translateEscape($source, $i, true);
                $items .= $escape[0];
                $i = $escape[1];
                $prevClassEscape = $escape[2];

                continue;
            }

            if ($ch === '[') {
                $items .= '\[';
            } elseif ($ch === '/') {
                $items .= '\/';
            } elseif ($ch === '-' && ($prevClassEscape || self::nextIsClassEscape($source, $i + 1) || (isset($source[$i + 1]) && $source[$i + 1] === ']'))) {
                $items .= '\-';
            } else {
                $items .= $ch;
            }
            $prevClassEscape = false;
        }

        if ($i >= $length) {
            // unterminated class, leave it to PCRE to report
            return array(substr($source, $start), $length - 1);
        }

        if ($items === '' && !$complements) {
            // `[]` never matches, `[^]` matches anything
            return array($negated ? '[\s\S]' : '(?!)', $i);
        }

        if (!$complements) {
            return array('[' . ($negated ? '^' : '') . $items . ']', $i);
        }

        if (!$negated) {
            // any of the items, or anything outside of \w or \d
            $alternatives = $items === '' ? array() : array('[' . $items . ']');
            foreach ($complements as $complement) {
                $alternatives[] = '[^' . $complement . ']';
            }

            return array('(?:' . implode('|', $alternatives) . ')', $i);
        }

        // none of the items and inside each of the complemented sets
        $result = '(?:';
        if ($items !== '') {
            $result .= '(?![' . $items . '])';
        }
        foreach (array_slice($complements, 1) as $complement) {
            $result .= '(?=[' . $complement . '])';
        }

        return array($result . '[' . $complements[0] . '])', $i);
    }

    /**
     * @param string $source
     * @param int    $i       position of the backslash
     * @param bool   $inClass
     *
     * @return array{0: string, 1: int, 2: bool} the PCRE escape, the position of its last character, whether it is a
     *                                           class escape (like \w)
     */
    private static function translateEscape($source, $i, $inClass)
    {
        if (!isset($source[$i + 1])) {
            return array('\\\\', $i, false);
        }

        $next = $source[$i + 1];

        if ($next === 'u') {
            if (preg_match('/\G\{([0-9A-Fa-f]+)\}/', $source, $m, 0, $i + 2)) {
                return array('\\x{' . $m[1] . '}', $i + 1 + strlen($m[0]), false);
            }
            if (preg_match('/\G[0-9A-Fa-f]{4}/', $source, $m, 0, $i + 2)) {
                return array('\\x{' . $m[0] . '}', $i + 5, false);
            }

            return array('u', $i + 1, false);
        }

        if ($next === 'x') {
            if (preg_match('/\G[0-9A-Fa-f]{2}/', $source, $m, 0, $i + 2)) {
                return array('\\x' . $m[0], $i + 3, false);
            }

            return array('x', $i + 1, false);
        }

        if ($next === 'c') {
            if (isset($source[$i + 2]) && ctype_alpha($source[$i + 2])) {
                return array('\\c' . $source[$i + 2], $i + 2, false);
            }

            return array('\\\\c', $i + 1, false);
        }

        if ($next === 'v') {
            return array('\\x0B', $i + 1, false);
        }

        if (strpos('dDwW', $next) !== false) {
            $set = ($next === 'd' || $next === 'D') ? '0-9' : self::WORD;
            if ($inClass) {
                // only \w and \d get here, \W and \D are handled by translateClass()
                return array($set, $i + 1, true);
            }

            return array('[' . (ctype_upper($next) ? '^' : '') . $set . ']', $i + 1, true);
        }

        if ($next === 's' || $next === 'S') {
            return array('\\' . $next, $i + 1, true);
        }

        if (strpos('tnrf0', $next) !== false) {
            return array('\\' . $next, $i + 1, false);
        }

        if ($next === 'b' || $next === 'B') {
            if ($inClass) {
                // in a JS character class `\b` is a backspace
                return array($next === 'b' ? '\\x08' : 'B', $i + 1, false);
            }
            $w = '[' . self::WORD . ']';
            if ($next === 'b') {
                return array("(?:(?<=$w)(?!$w)|(?<!$w)(?=$w))", $i + 1, false);
            }

            return array("(?:(?<=$w)(?=$w)|(?<!$w)(?!$w))", $i + 1, false);
        }

        if (ctype_digit($next)) {
            return array('\\' . $next, $i + 1, false);
        }

        if ($next === 'k' && isset($source[$i + 2]) && $source[$i + 2] === '<') {
            return array('\\k', $i + 1, false);
        }

        if (ctype_alpha($next)) {
            // an identity escape: a literal letter in JS
            return array($next, $i + 1, false);
        }

        // escaped punctuation (and non-ASCII bytes) are literals in both flavors
        return array('\\' . $next, $i + 1, false);
    }

    /**
     * @param string $source
     * @param int    $i
     *
     * @return bool
     */
    private static function nextIsClassEscape($source, $i)
    {
        return isset($source[$i + 1]) && $source[$i] === '\\' && strpos('dDwWsS', $source[$i + 1]) !== false;
    }
}

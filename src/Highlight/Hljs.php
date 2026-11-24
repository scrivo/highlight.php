<?php

/* Copyright (c) 2026 Geert Bergman (geert@scrivo.nl), highlight.php
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
 * The `hljs` object passed to language definitions.
 *
 * It mirrors what highlight.js offers language definitions at definition time:
 * the common regular expressions and modes, `inherit()`, `COMMENT()`,
 * `getLanguage()` and `requireLanguage()`. Property and method names follow
 * highlight.js so that language definitions read the same in both projects.
 *
 * Every language definition gets its own instance, so the common modes are
 * never shared between languages.
 *
 * @since 10.0.0
 */
final class Hljs
{
    // Common regexps
    /** @var string */
    public $IDENT_RE = '[a-zA-Z]\w*';
    /** @var string */
    public $UNDERSCORE_IDENT_RE = '[a-zA-Z_]\w*';
    /** @var string */
    public $NUMBER_RE = '\b\d+(\.\d+)?';
    /** @var string */
    public $C_NUMBER_RE = '(-?)(\b0[xX][a-fA-F0-9]+|(\b\d+(\.\d*)?|\.\d+)([eE][-+]?\d+)?)'; // 0x..., 0..., decimal, float
    /** @var string */
    public $BINARY_NUMBER_RE = '\b(0b[01]+)'; // 0b...
    /** @var string */
    public $RE_STARTERS_RE = '!|!=|!==|%|%=|&|&&|&=|\*|\*=|\+|\+=|,|-|-=|/=|/|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\?|\[|\{|\(|\^|\^=|\||\|=|\|\||~';

    // Common modes
    /** @var Mode */
    public $BACKSLASH_ESCAPE;
    /** @var Mode */
    public $APOS_STRING_MODE;
    /** @var Mode */
    public $QUOTE_STRING_MODE;
    /** @var Mode */
    public $PHRASAL_WORDS_MODE;
    /** @var Mode */
    public $C_LINE_COMMENT_MODE;
    /** @var Mode */
    public $C_BLOCK_COMMENT_MODE;
    /** @var Mode */
    public $HASH_COMMENT_MODE;
    /** @var Mode */
    public $NUMBER_MODE;
    /** @var Mode */
    public $C_NUMBER_MODE;
    /** @var Mode */
    public $BINARY_NUMBER_MODE;
    /** @var Mode */
    public $CSS_NUMBER_MODE;
    /** @var Mode */
    public $REGEXP_MODE;
    /** @var Mode */
    public $TITLE_MODE;
    /** @var Mode */
    public $UNDERSCORE_TITLE_MODE;
    /** @var Mode */
    public $METHOD_GUARD;

    /** @var callable(string): ?Language */
    private $languageResolver;

    /**
     * @param callable(string): ?Language|null $languageResolver resolves a language name or alias
     */
    public function __construct(?callable $languageResolver = null)
    {
        $this->languageResolver = $languageResolver ?: static function ($name) {
            return null;
        };

        $this->BACKSLASH_ESCAPE = new Mode(array(
            'begin' => '\\\\[\s\S]',
            'relevance' => 0,
        ));
        $this->APOS_STRING_MODE = new Mode(array(
            'className' => 'string',
            'begin' => '\'',
            'end' => '\'',
            'illegal' => '\n',
            'contains' => array($this->BACKSLASH_ESCAPE),
        ));
        $this->QUOTE_STRING_MODE = new Mode(array(
            'className' => 'string',
            'begin' => '"',
            'end' => '"',
            'illegal' => '\n',
            'contains' => array($this->BACKSLASH_ESCAPE),
        ));
        $this->PHRASAL_WORDS_MODE = new Mode(array(
            'begin' => '\b(a|an|the|are|I\'m|isn\'t|don\'t|doesn\'t|won\'t|but|just|should|pretty|simply|enough|gonna|going|wtf|so|such|will|you|your|they|like|more)\b',
        ));
        $this->C_LINE_COMMENT_MODE = $this->COMMENT('//', '$');
        $this->C_BLOCK_COMMENT_MODE = $this->COMMENT('/\*', '\*/');
        $this->HASH_COMMENT_MODE = $this->COMMENT('#', '$');
        $this->NUMBER_MODE = new Mode(array(
            'className' => 'number',
            'begin' => $this->NUMBER_RE,
            'relevance' => 0,
        ));
        $this->C_NUMBER_MODE = new Mode(array(
            'className' => 'number',
            'begin' => $this->C_NUMBER_RE,
            'relevance' => 0,
        ));
        $this->BINARY_NUMBER_MODE = new Mode(array(
            'className' => 'number',
            'begin' => $this->BINARY_NUMBER_RE,
            'relevance' => 0,
        ));
        $this->CSS_NUMBER_MODE = new Mode(array(
            'className' => 'number',
            'begin' => $this->NUMBER_RE . '('
                . '%|em|ex|ch|rem'
                . '|vw|vh|vmin|vmax'
                . '|cm|mm|in|pt|pc|px'
                . '|deg|grad|rad|turn'
                . '|s|ms'
                . '|Hz|kHz'
                . '|dpi|dpcm|dppx'
                . ')?',
            'relevance' => 0,
        ));
        $this->REGEXP_MODE = new Mode(array(
            // this outer rule makes sure we actually have a WHOLE regex and not simply
            // an expression such as:
            //
            //     3 / something
            //
            // (which will then blow up when regex's `illegal` sees the newline)
            'begin' => '(?=\/[^\/\n]*\/)',
            'contains' => array(
                new Mode(array(
                    'className' => 'regexp',
                    'begin' => '\/',
                    'end' => '\/[gimuy]*',
                    'illegal' => '\n',
                    'contains' => array(
                        $this->BACKSLASH_ESCAPE,
                        new Mode(array(
                            'begin' => '\[',
                            'end' => '\]',
                            'relevance' => 0,
                            'contains' => array($this->BACKSLASH_ESCAPE),
                        )),
                    ),
                )),
            ),
        ));
        $this->TITLE_MODE = new Mode(array(
            'className' => 'title',
            'begin' => $this->IDENT_RE,
            'relevance' => 0,
        ));
        $this->UNDERSCORE_TITLE_MODE = new Mode(array(
            'className' => 'title',
            'begin' => $this->UNDERSCORE_IDENT_RE,
            'relevance' => 0,
        ));
        $this->METHOD_GUARD = new Mode(array(
            // excludes method names from keyword processing
            'begin' => '\.\s*' . $this->UNDERSCORE_IDENT_RE,
            'relevance' => 0,
        ));
    }

    /**
     * Performs a shallow merge of multiple modes (or arrays of mode properties)
     * into a new mode.
     *
     * Properties that are references (arrays shared while a definition is being
     * built) stay shared, as they would in JavaScript.
     *
     * @param Mode|array<string, mixed> $parent
     * @param Mode|array<string, mixed> ...$overrides
     */
    public function inherit($parent, ...$overrides): Mode
    {
        $result = new Mode();

        foreach (array_merge(array($parent), $overrides) as $object) {
            foreach ((array) $object as $key => &$value) {
                $result->{$key} = &$value;
            }
            unset($value);
        }
        Mode::convertNestedModes($result);

        return $result;
    }

    /**
     * Creates a comment mode.
     *
     * @param string|null                    $begin
     * @param string|null                    $end
     * @param Mode|array<string, mixed>|null $inherits
     */
    public function COMMENT($begin = null, $end = null, $inherits = null): Mode
    {
        $mode = $this->inherit(
            array(
                'className' => 'comment',
                'begin' => $begin,
                'end' => $end,
                'contains' => array(),
            ),
            $inherits ?: array()
        );
        $mode->contains[] = $this->PHRASAL_WORDS_MODE;
        $mode->contains[] = new Mode(array(
            'className' => 'doctag',
            'begin' => '(?:TODO|FIXME|NOTE|BUG|XXX):',
            'relevance' => 0,
        ));

        return $mode;
    }

    /**
     * @param string $name a language name or alias
     */
    public function getLanguage($name): ?Language
    {
        return ($this->languageResolver)(strtolower((string) $name));
    }

    /**
     * @param string $name a language name or alias
     *
     * @throws \DomainException when the language is not available
     */
    public function requireLanguage($name): Language
    {
        $language = $this->getLanguage($name);

        if ($language === null) {
            throw new \DomainException("The '$name' language is required, but not loaded.");
        }

        return $language;
    }
}

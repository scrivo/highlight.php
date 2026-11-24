<?php

/* Copyright (c) 2013-2019 Geert Bergman (geert@scrivo.nl), highlight.php
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

namespace Highlight\Tests;

use Highlight\Highlighter;
use Highlight\Hljs;
use Highlight\Language;
use Highlight\LanguageDefinition;
use Highlight\Languages\Registry;
use Highlight\Mode;
use Highlight\RegEx;
use Highlight\RegExUtils;
use PHPUnit\Framework\TestCase;

class LanguageDefinitionTest extends TestCase
{
    protected function setUp(): void
    {
        Highlighter::clearAllLanguages();
    }

    public static function bundledLanguagesProvider()
    {
        foreach (Registry::LANGUAGES as $id => $class) {
            yield $id => array($id, $class);
        }
    }

    /**
     * @dataProvider bundledLanguagesProvider
     */
    public function testRegistryAliasesMatchDefinition($id, $class)
    {
        $language = $this->getLanguage(new Highlighter(), $id);
        $aliases = $language->aliases ?: array();

        $this->assertSame($aliases, Registry::ALIASES[$id]);
        $this->assertSame($id, $class::ID);
        $this->assertInstanceOf(LanguageDefinition::class, new $class());
    }

    /**
     * @dataProvider bundledLanguagesProvider
     */
    public function testAllRegularExpressionsCompile($id, $class)
    {
        $hl = new Highlighter();
        // highlighting compiles the language
        $hl->highlight($id, '');

        $language = $this->getLanguage($hl, $id);
        $count = 0;
        $this->walk($language->getMode(), function (Mode $mode) use (&$count) {
            foreach (array('beginRe', 'endRe', 'illegalRe', 'lexemesRe') as $key) {
                if (isset($mode->{$key}) && $mode->{$key} instanceof RegEx) {
                    $this->assertNotFalse(@preg_match($mode->{$key}->source, ''), "Invalid $key: {$mode->{$key}->source}");
                    ++$count;
                }
            }
        });
        $this->assertGreaterThan(0, $count);
    }

    public function testRegisterCustomLanguageDefinition()
    {
        $definition = new class implements LanguageDefinition {
            public function __invoke(Hljs $hljs): Mode
            {
                return new Mode(array(
                    'aliases' => array('tst'),
                    'keywords' => 'hello',
                    'contains' => array($hljs->QUOTE_STRING_MODE),
                ));
            }
        };

        Highlighter::registerLanguage('test', $definition);
        $hl = new Highlighter(false);
        $result = $hl->highlight('tst', 'hello "world"');

        $this->assertSame('<span class="hljs-keyword">hello</span> <span class="hljs-string">"world"</span>', $result->value);
    }

    public function testLanguageCanExtendAnotherLanguage()
    {
        // cpp is built on c-like through `getLanguage('c-like')->rawDefinition()`
        $hl = new Highlighter();
        $result = $hl->highlight('cpp', 'int main() {}');

        $this->assertStringContainsString('hljs-keyword', $result->value);
    }

    public function testSharedArraysKeepJavaScriptSemantics()
    {
        // json.js pushes OBJECT and ARRAY onto TYPES after TYPES was used in VALUE_CONTAINER
        $hl = new Highlighter();
        $result = $hl->highlight('json', '{"a": {"b": [1]}}');

        $this->assertSame(
            '{<span class="hljs-attr">"a"</span>: {<span class="hljs-attr">"b"</span>: [<span class="hljs-number">1</span>]}}',
            $result->value
        );
    }

    public static function jsRegexProvider()
    {
        return array(
            'delimiter' => array('a/b', 'a\/b'),
            'escaped delimiter' => array('a\/b', 'a\/b'),
            'empty class' => array('[]', '(?!)'),
            'negated empty class' => array('[^]', '[\s\S]'),
            'unicode escape' => array('\\u00C0', '\x{00C0}'),
            'unicode code point escape' => array('\u{1F600}', '\x{1F600}'),
            'bracket in class' => array('[a[]', '[a\[]'),
            'hyphen after class escape' => array('[\w-.]', '[a-zA-Z0-9_\-.]'),
            'hyphen at class end' => array('[a-]', '[a\-]'),
            'identity escape' => array('\e', 'e'),
            'vertical tab' => array('\v', '\x0B'),
            'backspace in class' => array('[\b]', '[\x08]'),
            'ASCII word characters' => array('\w+', '[a-zA-Z0-9_]+'),
            'ASCII non-word characters' => array('\W', '[^a-zA-Z0-9_]'),
            'ASCII digits' => array('\d', '[0-9]'),
            'ASCII word boundary' => array('\b', '(?:(?<=[a-zA-Z0-9_])(?![a-zA-Z0-9_])|(?<![a-zA-Z0-9_])(?=[a-zA-Z0-9_]))'),
            'complement in class' => array('[\W\d]', '(?:[0-9]|[^a-zA-Z0-9_])'),
            'complement in negated class' => array('[^\W:]', '(?:(?![:])[a-zA-Z0-9_])'),
            'backreference' => array('(a)\1', '(a)\1'),
        );
    }

    /**
     * @dataProvider jsRegexProvider
     */
    public function testJsToPcre($js, $pcre)
    {
        $this->assertSame($pcre, RegExUtils::jsToPcre($js));
        $this->assertNotFalse(@preg_match('/' . RegExUtils::jsToPcre($js) . '/u', ''));
    }

    public function testWordBoundariesAreAsciiOnlyLikeJavaScript()
    {
        // In JS "р1" has a word boundary between the Cyrillic letter and the digit
        $this->assertSame(1, preg_match('/' . RegExUtils::jsToPcre('\\b\\d+') . '/u', "\u{0440}1"));
        $this->assertSame(0, preg_match('/' . RegExUtils::jsToPcre('\\w') . '/u', "\u{0440}"));
    }

    /**
     * @return Language
     */
    private function getLanguage(Highlighter $hl, $id)
    {
        $method = new \ReflectionMethod(Highlighter::class, 'getLanguage');
        if (PHP_VERSION_ID < 80100) {
            // Required to invoke private methods before PHP 8.1, deprecated since PHP 8.5
            $method->setAccessible(true);
        }

        return $method->invoke($hl, $id);
    }

    private function walk(Mode $mode, callable $callback, ?\SplObjectStorage $seen = null)
    {
        $seen = $seen ?: new \SplObjectStorage();
        if (isset($seen[$mode])) {
            return;
        }
        $seen[$mode] = true;
        $callback($mode);

        foreach (array('contains', 'variants', 'cachedVariants') as $key) {
            if (isset($mode->{$key}) && is_array($mode->{$key})) {
                foreach ($mode->{$key} as $child) {
                    if ($child instanceof Mode) {
                        $this->walk($child, $callback, $seen);
                    }
                }
            }
        }
        if (isset($mode->starts) && $mode->starts instanceof Mode) {
            $this->walk($mode->starts, $callback, $seen);
        }
    }
}

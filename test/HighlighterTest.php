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

use Highlight\Highlighter;
use Highlight\Language;
use Symfony\Component\Finder\Finder;

class HighlighterTest extends BC_PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Highlighter::clearAllLanguages();
    }

    public function testUnknownLanguageThrowsDomainException()
    {
        $this->bc_expectException('\DomainException');

        $hl = new Highlighter();
        $hl->highlight("blah++", "als blurp eq z dan zeg 'flipper'");
    }

    public function testListLanguagesWithoutAliases()
    {
        $languageFinder = new Finder();
        $expectedLanguageCount = $languageFinder->in(__DIR__ . '/../Highlight/languages/')->name('*.json')->count();

        $hl = new Highlighter();

        try {
            $availableLanguages = $hl->listLanguages();
            $this->assertEquals($expectedLanguageCount, count($availableLanguages));
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        try {
            $availableLanguages = $hl->listLanguages(false);
            $this->assertEquals($expectedLanguageCount, count($availableLanguages));
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        $availableLanguages = Highlighter::listRegisteredLanguages();
        $this->assertEquals($expectedLanguageCount, count($availableLanguages));

        $availableLanguages = Highlighter::listRegisteredLanguages(false);
        $this->assertEquals($expectedLanguageCount, count($availableLanguages));
    }

    public function testListLanguagesWithAliases()
    {
        $languageFinder = new Finder();
        $minimumLanguageCount = $languageFinder->in(__DIR__ . '/../Highlight/languages/')->name('*.json')->count();

        $hl = new Highlighter();
        $availableLanguages = Highlighter::listRegisteredLanguages(true);

        try {
            $this->assertGreaterThan($minimumLanguageCount, count($hl->listLanguages(true)));
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        $this->assertGreaterThan($minimumLanguageCount, count($availableLanguages));

        // Verify some common aliases/names are present.
        $this->assertContains('yaml', $availableLanguages);
        $this->assertContains('yml', $availableLanguages);
        $this->assertContains('c++', $availableLanguages);
        $this->assertContains('cpp', $availableLanguages);
    }

    public function testGetAliasesForLanguageWhenUsingMainLanguageName()
    {
        $languageDefinitionFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                                'Highlight' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . "php.json";
        $language = new Language('php', $languageDefinitionFile);
        $expected_aliases = $language->aliases;
        $expected_aliases[] = 'php';
        sort($expected_aliases);

        $hl = new Highlighter();
        $aliases = $hl->getAliasesForLanguage('php');
        sort($aliases);

        $this->assertEquals($expected_aliases, $aliases);
    }

    public function testGetAliasesForLanguageWhenLanguageHasNoAliases()
    {
        $languageDefinitionFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                                'Highlight' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . "ada.json";
        $language = new Language('ada', $languageDefinitionFile);
        $expected_aliases = $language->aliases;
        $expected_aliases[] = 'ada';
        sort($expected_aliases);

        $hl = new Highlighter();
        $aliases = $hl->getAliasesForLanguage('ada');
        sort($aliases);

        $this->assertEquals($expected_aliases, $aliases);
    }

    public function testGetAliasesForLanguageWhenUsingLanguageAlias()
    {
        $languageDefinitionFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                                'Highlight' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . "php.json";
        $language = new Language('php', $languageDefinitionFile);
        $expected_aliases = $language->aliases;
        $expected_aliases[] = 'php';
        sort($expected_aliases);

        $hl = new Highlighter();
        $aliases = $hl->getAliasesForLanguage('php3');
        sort($aliases);

        $this->assertEquals($expected_aliases, $aliases);
    }

    public function testGetAliasesForLanguageRaisesExceptionForNonExistingLanguage()
    {
        $this->bc_expectException('\DomainException');

        $hl = new Highlighter();
        $hl->getAliasesForLanguage('blah+');
    }

    public function testLoadAllLanguagesByDefault()
    {
        $hl = new Highlighter();
        $langs = new Finder();
        $langs
            ->in(__DIR__ . '/../Highlight/languages/')
            ->files()
        ;

        try {
            $this->assertEquals($hl->listLanguages(), Highlighter::listBundledLanguages());
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        $this->assertCount($langs->count(), Highlighter::listBundledLanguages());
        $this->assertCount($langs->count(), Highlighter::listRegisteredLanguages());
    }

    public function testLoadNoLanguagesInConstructor()
    {
        $hl = new Highlighter(false);

        try {
            $this->assertCount(0, $hl->listLanguages());
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        $this->assertCount(0, Highlighter::listRegisteredLanguages());
    }

    public function testLoadOneLanguageManually()
    {
        Highlighter::registerLanguage('1c', __DIR__ . '/../Highlight/languages/1c.json');

        $hl = new Highlighter(false);

        try {
            $this->assertCount(1, $hl->listLanguages());
        } catch (Exception $e) {
            $this->assertEquals(E_USER_DEPRECATED, $e->getCode());
        }

        $this->assertCount(1, Highlighter::listRegisteredLanguages());
    }
}

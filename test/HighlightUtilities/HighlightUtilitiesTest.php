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

namespace Highlight\Tests;

class HighlightUtilitiesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Highlight\Highlighter */
    private $hl;

    protected function setUp()
    {
        $this->hl = new \Highlight\Highlighter();
    }

    public function testGetAvailableStyleSheetsNamesOnly()
    {
        $results = \HighlightUtilities\Functions::getAvailableStyleSheets();

        $this->assertNotEmpty($results);

        foreach ($results as $result) {
            $this->assertNotContains(DIRECTORY_SEPARATOR, $result);
            $this->assertNotContains(".css", $result);
        }
    }

    public function testGetAvailableStyleSheetsFilePaths()
    {
        $results = \HighlightUtilities\Functions::getAvailableStyleSheets(true);

        $this->assertNotEmpty($results);

        foreach ($results as $result) {
            $this->assertContains(DIRECTORY_SEPARATOR, $result);
            $this->assertContains(".css", $result);

            $this->assertFileExists($result);
        }
    }

    public function testGetAvailableStyleSheetsSameCount()
    {
        $namesOnly = \HighlightUtilities\Functions::getAvailableStyleSheets();
        $filePaths = \HighlightUtilities\Functions::getAvailableStyleSheets(true);

        $this->assertCount(count($namesOnly), $filePaths);
    }

    public function testGetStyleSheetExists()
    {
        $yesExt = \HighlightUtilities\Functions::getStyleSheet("a11y-dark.css");
        $noExt = \HighlightUtilities\Functions::getStyleSheet("a11y-dark");

        $this->assertNotEmpty($yesExt);
        $this->assertEquals($yesExt, $noExt);
    }

    public function testGetStyleSheetNotExists()
    {
        $this->setExpectedException('\DomainException');

        \HighlightUtilities\Functions::getStyleSheet("strawberry.png");
    }

    public function testSplitCodeIntoArrayMultilineComment()
    {
        $raw = <<<PHP
/**
 * Hello World
 *
 * @api
 * @since 1.0.0
 * @param string \$str Some string parameter
 */
PHP;
        $highlighted = $this->hl->highlight('php', $raw);

        $cleanSplit = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);
        $dumbSplit = preg_split('/\R/', $highlighted->value);

        $this->assertEquals(1, substr_count($highlighted->value, 'hljs-comment'));
        $this->assertEquals(count($cleanSplit), substr_count(implode(PHP_EOL, $cleanSplit), 'hljs-comment'));

        $this->assertTrue(is_array($cleanSplit));
        $this->assertCount(count($dumbSplit), $cleanSplit);
        $this->assertNotEquals($cleanSplit, $dumbSplit);

        foreach ($cleanSplit as $line) {
            $this->assertStringStartsWith('<span class="hljs-comment">', trim($line));
            $this->assertStringEndsWith('</span>', trim($line));
        }
    }

    public function testSplitCodeIntoArrayEmojis()
    {
        $raw = <<<'PHP'
// ✅ ...
$user = new \stdClass();
$isUserPending = $user->isStatus('pending');
PHP;
        $highlighted = $this->hl->highlight('php', $raw);
        $split = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);

        $this->assertEquals(
            array(
                '<span class="hljs-comment">// ✅ ...</span>',
                '$user = <span class="hljs-keyword">new</span> \stdClass();',
                '$isUserPending = $user-&gt;isStatus(<span class="hljs-string">\'pending\'</span>);',
            ),
            $split
        );
    }

    public function testSplitCodeIntoArrayDeeplyNestedSpans()
    {
        $raw = <<<'JAVA'
public QuoteEntity(
)
JAVA;
        $highlighted = $this->hl->highlight('java', $raw);
        $split = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);

        $this->assertEquals(
            array(
                '<span class="hljs-function"><span class="hljs-keyword">public</span> <span class="hljs-title">QuoteEntity</span><span class="hljs-params">(</span></span>',
                '<span class="hljs-function"><span class="hljs-params">)</span></span>',
            ),
            $split
        );
    }

    public function testSplitCodeIntoArrayXmlWithAttributesOnNewLines()
    {
        $raw = <<<'XML'
<?xml version="1.0" encoding="utf-8" ?>
               <tag a="t"
                        b="t">
                 </tag>
XML;
        $highlighted = $this->hl->highlight('xml', $raw);
        $split = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);

        $this->assertEquals(
            array(
                '<span class="hljs-meta">&lt;?xml version="1.0" encoding="utf-8" ?&gt;</span>',
                '               <span class="hljs-tag">&lt;<span class="hljs-name">tag</span> <span class="hljs-attr">a</span>=<span class="hljs-string">"t"</span></span>',
                '<span class="hljs-tag">                        <span class="hljs-attr">b</span>=<span class="hljs-string">"t"</span>&gt;</span>',
                '                 <span class="hljs-tag">&lt;/<span class="hljs-name">tag</span>&gt;</span>',
            ),
            $split
        );
    }

    public function testSplitCodeIntoArrayXmlWithAttributesSpanningMultipleLines()
    {
        $raw = <<<'XML'
<?xml version="1.0" encoding="utf-8" ?>
<nlog xmlns="http://www.nlog-project.org/schemas/NLog.xsd"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.nlog-project.org/schemas/NLog.xsd NLog.xsd"
      autoReload="true">
</nlog>
XML;
        $highlighted = $this->hl->highlight('xml', $raw);
        $split = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);

        $this->assertEquals(
            array(
                '<span class="hljs-meta">&lt;?xml version="1.0" encoding="utf-8" ?&gt;</span>',
                '<span class="hljs-tag">&lt;<span class="hljs-name">nlog</span> <span class="hljs-attr">xmlns</span>=<span class="hljs-string">"http://www.nlog-project.org/schemas/NLog.xsd"</span></span>',
                '<span class="hljs-tag">      <span class="hljs-attr">xmlns:xsi</span>=<span class="hljs-string">"http://www.w3.org/2001/XMLSchema-instance"</span></span>',
                '<span class="hljs-tag">      <span class="hljs-attr">xsi:schemaLocation</span>=<span class="hljs-string">"http://www.nlog-project.org/schemas/NLog.xsd NLog.xsd"</span></span>',
                '<span class="hljs-tag">      <span class="hljs-attr">autoReload</span>=<span class="hljs-string">"true"</span>&gt;</span>',
                '<span class="hljs-tag">&lt;/<span class="hljs-name">nlog</span>&gt;</span>',
            ),
            $split
        );
    }

    public function testSplitCodeIntoArrayDeeplyNestedSpansCRLF()
    {
        $raw = "public QuoteEntity(\r\n)";

        $highlighted = $this->hl->highlight('java', $raw);
        $split = \HighlightUtilities\Functions::splitCodeIntoArray($highlighted->value);

        $this->assertEquals(
            array(
                '<span class="hljs-function"><span class="hljs-keyword">public</span> <span class="hljs-title">QuoteEntity</span><span class="hljs-params">(</span></span>',
                '<span class="hljs-function"><span class="hljs-params">)</span></span>',
            ),
            $split
        );
    }

    public static function dataProvider_emptyStrings()
    {
        return array(
            array(""),
            array("\t"),
            array("  "),
        );
    }

    /**
     * @dataProvider dataProvider_emptyStrings
     */
    public function testSplitCodeIntoArrayEmptyString($string)
    {
        $this->assertEquals(array(), \HighlightUtilities\Functions::splitCodeIntoArray($string));
    }

    public function testGetThemeBackgroundColorSingleColor()
    {
        $theme = 'atom-one-dark';

        $this->assertEquals(array('r' => 40, 'g' => 44, 'b' => 52), \HighlightUtilities\Functions::getThemeBackgroundColor($theme));
    }

    public function testGetThemeBackgroundColorColorWithBgImage()
    {
        $theme = 'brown-paper';

        $this->assertEquals(array('r' => 183, 'g' => 166, 'b' => 142), \HighlightUtilities\Functions::getThemeBackgroundColor($theme));
    }
}

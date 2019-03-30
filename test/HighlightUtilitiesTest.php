<?php

class HighlightUtilitiesTest extends PHPUnit_Framework_TestCase
{
    /** @var \Highlight\Highlighter */
    private $hl;

    protected function setUp()
    {
        $this->hl = new \Highlight\Highlighter();
    }

    public function testSplitCodeIntoArray_MultilineComment()
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

        $cleanSplit = \HighlightUtilities\splitCodeIntoArray($highlighted->value);
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
}

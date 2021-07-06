<?php

use Highlight\Highlighter;

class UnicodeTest extends \PHPUnit_Framework_TestCase
{
    /** @var Highlighter */
    private $hl;

    protected function setUp()
    {
        $this->hl = new Highlighter();
    }

    public function testUnicodeCharacters()
    {
        $raw = <<<CSTYLE
if (FELTÉTEL)
{
  IGAZ ÁG
}
else
{
  HAMIS ÁG
}
CSTYLE;
        $result = $this->hl->highlight('c', $raw)->value;
        $expected = <<<EXPECTED
<span class="hljs-keyword">if</span> (FELTÉTEL)
{
  IGAZ ÁG
}
<span class="hljs-keyword">else</span>
{
  HAMIS ÁG
}
EXPECTED;

        $this->assertEquals($expected, $result);
    }
}

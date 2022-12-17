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

class SpecialTest extends BC_PHPUnit_Framework_TestCase
{
    private function getTestData($name)
    {
        return (object) array(
            "code" => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .
                "special" . DIRECTORY_SEPARATOR . "{$name}.txt"),
            "expected" => file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .
                "special" . DIRECTORY_SEPARATOR . "{$name}.expect.txt"),
        );
    }

    public function testTabReplace()
    {
        $hl = new Highlighter();
        $hl->setTabReplace("    ");

        $data = $this->getTestData("tabreplace");
        $actual = $hl->highlight("python", $data->code);

        $this->assertEquals($data->expected, $actual->value);
    }

    public function testLanguageAlias()
    {
        $hl = new Highlighter();

        $data = $this->getTestData("languagealias");
        $actual = $hl->highlight("js", $data->code);

        $this->assertEquals($data->expected, $actual->value);
    }

    public function testSubLanguage()
    {
        $hl = new Highlighter();

        $data = $this->getTestData("sublanguages");
        $actual = $hl->highlight("xml", $data->code);

        $this->assertEquals($data->expected, $actual->value);
    }

    public function testWindowsCRLF()
    {
        $hl = new Highlighter();

        $data = $this->getTestData("line-endings.crlf");
        $actual = $hl->highlight("js", $data->code);

        $this->assertEquals($data->expected, $actual->value);
    }
}

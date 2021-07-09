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
use Symfony\Component\Finder\Finder;

class CasesFromIssuesTest extends PHPUnit_Framework_TestCase
{
    private static function parseFileName($filename)
    {
        $re = '/(\d+)(?:-\[(\w+)])?(\.expected)?\.txt/';
        $matches = array();

        preg_match($re, $filename, $matches);

        return array(
            'issueNumber' => $matches[1],
            'languages' => isset($matches[2]) ? $matches[2] : null,
            'expected' => isset($matches[3]),
        );
    }

    public static function projectSpecificIssueProvider()
    {
        $specialCases = array();

        $cases = new Finder();
        $cases
            ->in(__DIR__ . '/issues/')
            ->sortByName()
            ->files()
        ;

        foreach ($cases as $case) {
            $config = self::parseFileName($case->getFilename());

            if (!isset($specialCases[$config['issueNumber']])) {
                $specialCases[$config['issueNumber']] = array(
                    'issueNumber' => $config['issueNumber'],
                    'source' => '',
                    'expected' => '',
                    'languages' => $config['languages'],
                );
            }

            if ($config['expected']) {
                $specialCases[$config['issueNumber']]['expected'] = trim($case->getContents());
            } else {
                $specialCases[$config['issueNumber']]['source'] = trim($case->getContents());
            }
        }

        return $specialCases;
    }

    /**
     * @dataProvider projectSpecificIssueProvider
     */
    public function testIssueSpecificCases($issueNumber, $source, $expected, $languages)
    {
        $hl = new Highlighter();
        $actual = $hl->highlight($languages, $source);

        $errMessage = sprintf("Unit test added for Issue #%d failed", $issueNumber);

        $this->assertEquals($expected, $actual->value, $errMessage);
    }
}

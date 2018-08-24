<?php
/* Copyright (c)
 * - 2013-2015, Geert Bergman (geert@scrivo.nl), highlight.php
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

require_once "../Highlight/Autoloader.php";
spl_autoload_register("Highlight\\Autoloader::load");

$hl = new Highlight\Highlighter();

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../styles/default.css">

        <style type="text/css">
            table {
                border-collapse:collapse;
                border-spacing: 0;
                font-family: sans-serif;
                width: 100%;
            }

            th, td {
                border: none;
                overflow: auto;
            }

            td[data-line-number] {
                border-right: 1px solid grey;
                padding: 0 15px;
                text-align: right;
                width: 1%;
            }

            td[data-line-number]::before {
                content: attr(data-line-number);
                display: block;
            }

            .blob-code {
                padding-left: 15px;
                white-space: pre;
            }
        </style>
    </head>

    <body>
        <?php
        $snippet = file_get_contents("../test/detect/ruby/default.txt");
        $r = $hl->highlight('ruby', $snippet);

        $lines = preg_split('/\R/', $r->value);
        ?>
        <table>
            <tbody>
                <?php foreach ($lines as $number => $line): ?>
                    <tr>
                        <td id="L<?= $number ?>" data-line-number="<?= $number ?>"></td>
                        <td id="LC<?= $number ?>" class="blob-code"><?= $line ?></td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </body>
</html>

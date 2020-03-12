<?php

/* Copyright (c)
 * - 2013-2019, Geert Bergman (geert@scrivo.nl), highlight.php
 * - 2014,      Daniel Lynge, highlight.php (contributor)
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

function getLanguageRaw($lang)
{
    return file_get_contents("../test/detect/{$lang}/default.txt");
}

function getLanguageDemo($lang)
{
    $snippet = getLanguageRaw($lang);

    if ($snippet === false) {
        die('Language not found');
    }

    global $hl;

    $result = $hl->highlight($lang, $snippet);

    return vsprintf(
        '
            <link rel="stylesheet" type="text/css" href="../styles/default.css">
            <style>* { margin: 0; padding: 0; }</style>
            <pre><code class="%s hljs">%s</code></pre>
        ',
        array(
            $result->language,
            $result->value,
        )
    );
}

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];

    if (isset($_GET['raw'])) {
        echo getLanguageRaw($lang);
        die();
    }

    echo getLanguageDemo($_GET['lang']);
    die();
}

?>
<html lang="en">
    <head>
        <title>highlight.php vs highlight.js Comparison</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css">

        <script src="highlight.pack.js"></script>
        <script>
            hljs.tabReplace = '    ';
            hljs.initHighlightingOnLoad();

            window.addEventListener('load', function () {
                const rows = document.querySelectorAll('[data-lang-comparison]');

                for (let i = 0; i < rows.length; i++) {
                    const row = rows.item(i);
                    const cols = row.getElementsByTagName('td');

                    const resultCol = cols.item(0);
                    const phpCodeCol = cols.item(1);
                    const jsCodeCol = cols.item(2);

                    const iframe = phpCodeCol.getElementsByTagName('iframe').item(0);
                    const phpCode = iframe.contentDocument.getElementsByTagName('pre').item(0);
                    const jsCode = jsCodeCol.getElementsByTagName('pre').item(0);

                    if (phpCode.innerHTML === jsCode.innerHTML) {
                        resultCol.style.backgroundColor = '#ccffcc';
                    } else {
                        resultCol.style.backgroundColor = '#ffcccc';
                    }
                }
            });
        </script>

        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
            }

            p {
                margin-bottom: 0.8rem;
            }

            table {
                border-spacing: 0;
                border-collapse: collapse;
                font-family: sans-serif;
                height: 1px;
                width: 100%
            }

            th, td {
                border: solid grey 1px;
                overflow: auto;
                max-width: 500px
            }

            pre {
                height: 100%;
            }

            pre code,
            pre div {
                padding: 0.5em;
                background: #F0F0F0;
            }

            td.signal {
                padding: 0.5em;
                background-color: #fff900;
            }

            iframe {
                border: 0;
                height: 100%;
                width: 100%;
            }
        </style>
    </head>

    <body>
        <div style="padding: 3rem;">
            <p><strong>HEADS UP!!!</strong></p>
            <p>
                This page is <em>extremely</em> slow and it will take a few seconds to fully load. This page is slow
                because it needs to load <?= count($hl->listLanguages()); ?> <code>iframe</code>s; each iframe needs to
                load their own CSS and highlight their own PHP.
            </p>
            <p>
                <strong>After</strong> all of the iframes have loaded, then this page has JS to iterate through each
                language and compare the results of highlight.php against highlight.js.
            </p>
            <p>
                This page does <strong>not</strong> indicate a performance issue with highlight.php. This page is
                designed this way so that you can load this page without JavaScript enabled and you'll still be able to
                see the code samples highlighted.
            </p>
        </div>

        <table>
            <tr>
                <th>result</th>
                <th>highlight.php</th>
                <th>highlight.js</th>
            </tr>
            <?php foreach ($hl->listLanguages() as $languageId): ?>
                <tr data-lang-comparison>
                    <td class="signal"><?= $languageId; ?></td>
                    <td>
                        <iframe src="compare.php?lang=<?= $languageId; ?>"></iframe>
                    </td>
                    <td class="js">
                        <pre><code class="<?= $languageId; ?>"><?= htmlentities(getLanguageRaw($languageId)); ?></code></pre>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </body>
</html>

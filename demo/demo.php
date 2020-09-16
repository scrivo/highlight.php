<?php

/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013-2019, Geert Bergman (geert@scrivo.nl), highlight.php
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

require_once "../vendor/autoload.php";

use Highlight\Highlighter;

$hl = new Highlighter();
$hl->setAutodetectLanguages($hl->listLanguages());

function getLanguageRaw($lang)
{
    return file_get_contents("../test/detect/{$lang}/default.txt");
}

function getLanguageDemo($lang)
{
    $snippet = getLanguageRaw($lang);

    if ($snippet === false) {
        die("Language not found");
    }

    global $hl;

    $start = microtime(true);
    $result = $hl->highlightAuto($snippet);
    $totalTime = microtime(true) - $start;

    return strtr(
        '
            <link rel="stylesheet" type="text/css" href="../styles/default.css">
            <style>* { margin: 0; padding: 0 }</style>
            <p>Result: {result} <small>[Expected: {expected}; Actual: {actual} ({actRel}); Second Best: {second} ({secRel})]</small></p>
            <p>Total Time: {time} secs</p>
            <pre><code class="{actual} hljs">{code}</code></pre>
        ',
        array(
            '{result}' => $result->language === $lang ? 'Passed' : 'FAILED',
            '{expected}' => $lang,
            '{actual}' => $result->language,
            '{actRel}' => $result->relevance,
            '{second}' => $result->secondBest->language,
            '{secRel}' => $result->secondBest->relevance,
            '{code}' => $result->value,
            '{time}' => sprintf('%.3f', $totalTime),
        )
    );
}

if (isset($_GET['lang'])) {
    set_time_limit(90);

    $lang = $_GET['lang'];

    if (isset($_GET['raw'])) {
        echo getLanguageRaw($lang);
        die();
    }

    echo getLanguageDemo($_GET['lang']);
    die();
}

$styles = HighlightUtilities\getAvailableStyleSheets();
sort($styles);

$languageCount = count($hl->listLanguages());
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>highlight.js test</title>
        <meta charset="utf-8">

        <style>
            iframe {
                border: 0;
                width: 100%;
            }

            .style-selector {
                display: flex;
                flex-wrap: wrap;
            }

            .style-selector label {
                width: 25%;
            }
        </style>
    </head>

    <body>
        <h1>highlight.php Auto-Detection</h1>

        <p>
            This is a demo/test page showing all languages supported by
            <a href="https://github.com/scrivo/highlight.php">highlight.php</a>. Most snippets do not contain working
            code :-).
        </p>

        <p>
            This page will take an <strong>EXTREMELY</strong> long time to load since it is automatically detecting
            <?= $languageCount; ?> languages. Automatic detection happens in a brute force fashion meaning loading this
            page will cause <?= pow($languageCount, 2); ?> iterations.
        </p>

        <p>For example, this page takes approximately 9 minutes to load completely for @allejo</p>

        <form id="stylesheet-switcher">
            <fieldset>
                <legend>Stylesheet</legend>

                <div class="style-selector">
                    <?php foreach ($styles as $style): ?>
                        <label>
                            <input
                                type="radio"
                                name="stylesheet"
                                value="<?= $style; ?>"
                                <?= $style !== 'default' ?: 'checked'; ?>
                            />
                            <?= $style; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </form>

        <h2>Automatically detected languages</h2>

        <?php foreach ($hl->listLanguages() as $language): ?>
            <section>
                <h3><?= $language; ?></h3>
                <iframe src="demo.php?lang=<?= $language; ?>"></iframe>
            </section>
        <?php endforeach; ?>

        <script>
            const iframes = document.getElementsByTagName('iframe');

            for (let i = 0; i < iframes.length; i++) {
                iframes.item(i).addEventListener('load', function () {
                    this.height = this.contentDocument.body.scrollHeight + 'px';
                });
            }

            document
                .getElementById("stylesheet-switcher")
                .addEventListener('change', function () {
                    const newStylesheet = this.stylesheet.value;

                    for (let i = 0; i < iframes.length; i++) {
                        const iframe = iframes.item(i);
                        const link = iframe.contentDocument.getElementsByTagName('link').item(0);

                        link.href = '../styles/' + newStylesheet + '.css';
                    }
                });
        </script>
    </body>
</html>

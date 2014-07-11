<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013-2014, Geert Bergman (geert@scrivo.nl), highlight.php
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

set_time_limit(60);
$start = microtime(true);

require_once("../Highlight/Autoloader.php");
spl_autoload_register("Highlight\\Autoloader::load");

use Highlight\Highlighter;

$test = json_decode(file_get_contents("snippets/snippets.json"), true);

$hl = new Highlighter();
$hl->setAutodetectLanguages(array_values($test));

$tableRows = "";
$failed = array();

foreach ($test as $title => $name) {
    $sn = strpos($title, "HTML") !== false ? "html" : $name;
    $snippet = file_get_contents("snippets/{$sn}.txt");
    $r = $hl->highlightAuto($snippet);
    $passed = ($r->language === $name);
    $res = "<div class=\"test\"><var class=\"".($passed?"passed":"failed").
        "\">{$r->language}</var>"." ({$r->relevance})<br>";
    if (isset($r->secondBest)) {
        $res .= "{$r->secondBest->language}"." ({$r->secondBest->relevance})";
    }
    $tableRows .= "<tr><th>{$title}{$res}</th><td class=\"{$name}\">
        <pre><code class=\"hljs {$name}\">{$r->value}</code></pre></td></th>";
    if (!$passed) {
        $failed[] = $name;
    }
}

if (count($failed)) {
    $testResult = "<p id=\"summary\" class=\"failed\">Failed tests: ".
        implode(", ", $failed);
} else {
    $testResult = "<p id=\"summary\" class=\"passed\">All tests passed";
}

$testResult .= "</p><p>Highlighting took ".
    (microtime(true)-$start)." seconds</p>"; 

?>
<!DOCTYPE html>
<head>
  <title>highlight.js test</title>
  <meta charset="utf-8">

  <link rel="stylesheet" title="Default" href="../styles/default.css">
  <link rel="alternate stylesheet" title="Dark"
    href="../styles/dark.css">
  <link rel="alternate stylesheet" title="FAR"
    href="../styles/far.css">
  <link rel="alternate stylesheet" title="IDEA"
    href="../styles/idea.css">
  <link rel="alternate stylesheet" title="Sunburst"
    href="../styles/sunburst.css">
  <link rel="alternate stylesheet" title="Zenburn"
    href="../styles/zenburn.css">
  <link rel="alternate stylesheet" title="Visual Studio"
    href="../styles/vs.css">
  <link rel="alternate stylesheet" title="Ascetic"
    href="../styles/ascetic.css">
  <link rel="alternate stylesheet" title="Magula"
    href="../styles/magula.css">
  <link rel="alternate stylesheet" title="GitHub"
    href="../styles/github.css">
  <link rel="alternate stylesheet" title="Google Code"
    href="../styles/googlecode.css">
  <link rel="alternate stylesheet" title="Brown Paper"
    href="../styles/brown_paper.css">
  <link rel="alternate stylesheet" title="School Book"
    href="../styles/school_book.css">
  <link rel="alternate stylesheet" title="IR Black"
    href="../styles/ir_black.css">
  <link rel="alternate stylesheet" title="Solarized - Dark"
    href="../styles/solarized_dark.css">
  <link rel="alternate stylesheet" title="Solarized - Light"
    href="../styles/solarized_light.css">
  <link rel="alternate stylesheet" title="Arta"
    href="../styles/arta.css">
  <link rel="alternate stylesheet" title="Monokai"
    href="../styles/monokai.css">
  <link rel="alternate stylesheet" title="Monokai Sublime"
    href="../styles/monokai_sublime.css">
  <link rel="alternate stylesheet" title="XCode"
    href="../styles/xcode.css">
  <link rel="alternate stylesheet" title="Pojoaque"
    href="../styles/pojoaque.css">
  <link rel="alternate stylesheet" title="Rainbow"
    href="../styles/rainbow.css">
  <link rel="alternate stylesheet" title="Tomorrow"
    href="../styles/tomorrow.css">
  <link rel="alternate stylesheet" title="Tomorrow Night"
    href="../styles/tomorrow-night.css">
  <link rel="alternate stylesheet" title="Tomorrow Night Bright"
    href="../styles/tomorrow-night-bright.css">
  <link rel="alternate stylesheet" title="Tomorrow Night Blue"
    href="../styles/tomorrow-night-blue.css">
  <link rel="alternate stylesheet" title="Tomorrow Night Eighties"
    href="../styles/tomorrow-night-eighties.css">
  <link rel="alternate stylesheet" title="Railscasts"
    href="../styles/railscasts.css">
  <link rel="alternate stylesheet" title="Obsidian"
    href="../styles/obsidian.css">
  <link rel="alternate stylesheet" title="Docco"
    href="../styles/docco.css">
  <link rel="alternate stylesheet" title="Mono Blue"
    href="../styles/mono-blue.css">
  <link rel="alternate stylesheet" title="Foundation"
    href="../styles/foundation.css">
  <link rel="alternate stylesheet" title="Atelier Dun - Dark"
    href="../styles/atelier-dune.dark.css">
  <link rel="alternate stylesheet" title="Atelier Dun - Light"
    href="../styles/atelier-dune.light.css">
  <link rel="alternate stylesheet" title="Atelier Forest - Dark"
    href="../styles/atelier-forest.dark.css">
  <link rel="alternate stylesheet" title="Atelier Forest - Light"
    href="../styles/atelier-forest.light.css">
  <link rel="alternate stylesheet" title="Atelier Heath - Dark"
    href="../styles/atelier-heath.dark.css">
  <link rel="alternate stylesheet" title="Atelier Heath - Light"
    href="../styles/atelier-heath.light.css">
  <link rel="alternate stylesheet" title="Atelier Lakeside - Dark"
    href="../styles/atelier-lakeside.dark.css">
  <link rel="alternate stylesheet" title="Atelier Lakeside - Light"
    href="../styles/atelier-lakeside.light.css">
  <link rel="alternate stylesheet" title="Atelier Seaside - Dark"
    href="../styles/atelier-seaside.dark.css">
  <link rel="alternate stylesheet" title="Atelier Seaside - Light"
    href="../styles/atelier-seaside.light.css">
  <link rel="alternate stylesheet" title="Paraíso - Dark"
    href="../styles/paraiso.dark.css">
  <link rel="alternate stylesheet" title="Paraíso - Light"
    href="../styles/paraiso.light.css">
  <link rel="alternate stylesheet" title="Colorbrewer"
    href="../styles/color-brewer.css">
  <link rel="alternate stylesheet" title="Codepen.io Embed"
    href="../styles/codepen-embed.css">
  <link rel="alternate stylesheet" title="Kimbie - Dark"
    href="../styles/kimbie.dark.css">
  <link rel="alternate stylesheet" title="Kimbie - Light"
    href="../styles/kimbie.light.css">
  <link rel="alternate stylesheet" title="Hybrid"
    href="../styles/hybrid.css">
  
  <style>
    /* Base styles */
    body {
      font: small Arial, sans-serif;
    }
    h2 {
      font: bold 100% Arial, sans-serif;
      margin-top: 2em;
      margin-bottom: 0.5em;
    }
    table {
      width: 100%;
      padding: 0;
      border-collapse: collapse;
    }
    th {
      width: 12em;
      padding: 0; margin: 0;
    }
    td {
      padding-bottom: 1em;
    }
    td, th {
      vertical-align: top;
      text-align: left;
    }
    pre {
      margin: 0;
      font-size: medium;
    }
    .hljs-debug {
      color: red;
    }
    /* Style switcher */
    ul#switch {
      width: 66em;
      -webkit-column-width: 15em;
      -webkit-column-gap: 2em;
      -moz-column-width: 15em;
      -moz-column-gap: 2em;
      -o-column-width: 15em;
      -o-column-gap: 2em;
      column-width: 15em;
      column-gap: 2em;
      list-style: none;
      overflow: auto;
      padding: 0;
      margin: 0;
    }
    ul#switch li {
      -webkit-column-break-inside: avoid;
      -moz-column-break-inside: avoid;
      -o-column-break-inside: avoid;
      column-break-inside: avoid;
      padding: 0.1em;
      margin: 0.1em 1em 0.1em 0;
      background: #EEE;
      cursor: pointer;
    }
    ul#switch li.current {
      background: #CCC;
    }
    /* Tests */
    .test {
      color: #888;
      font-weight: normal;
      margin: 2em 0 0 0;
    }
    .test var {
      font-style: normal;
    }
    .passed {
      color: green;
    }
    .failed, .failed a {
      color: red;
    }
    .code {
      font: medium monospace;
    }
    .code .hljs-keyword {
      font-weight: bold;
    }
    /* Export form */
    #export_from, #export_to {
      width: 98%;
    }
    address {
      margin-top: 4em;
    }
  </style>

  <script>
  // Stylesheet switcher © Vladimir Epifanov <voldmar@voldmar.ru>
  (function(container_id) {
      if (window.addEventListener) {
          var attach = function(el, ev, handler) {
              el.addEventListener(ev, handler, false);
          }
      } else if (window.attachEvent) {
          var attach = function(el, ev, handler) {
              el.attachEvent('on' + ev, handler);
          }
      } else {
          var attach = function(el, ev, handler) {
              ev['on' + ev] = handler;
          }
      }

      attach(window, 'load', function() {
          var current = null;

          var info = {};
          var links = document.getElementsByTagName('link');
          var ul = document.createElement('ul');

          for (var i = 0; (link = links[i]); i++) {
              if ((link.getAttribute('rel').indexOf('style') != -1) && link.title) {
                  var title = link.title;

                  info[title] = {
                      'link': link,
                      'li': document.createElement('li')
                  };

                  ul.appendChild(info[title].li);
                  info[title].li.title = title;

                  info[title].link.disabled = true;

                  info[title].li.appendChild(document.createTextNode(title));

                  attach(info[title].li, 'click', (function (el) {
                      return function() {
                          current.li.className = '';
                          current.link.disabled = true;
                          current = el;
                          current.li.className = 'current';
                          current.link.disabled = false;
                      }
                  })(info[title]));
              }
          }

          current = info['Default'];
          current.li.className = 'current';
          current.link.disabled = false;

          ul.id = 'switch';
          container = document.getElementById(container_id);
          container.appendChild(ul);
      });
  })('styleswitcher');
  </script>
<body>

<p>This is a demo/test page showing all languages supported by 
<a href="https://github.com/scrivo/highlight.php">highlight.php</a>.
Most snippets do not contain working code :-).

<div id="styleswitcher">
  <h2>Styles</h2>
</div>

<h2>Automatically detected languages</h2>

<?php echo $testResult;?>
<table id="autotest"><?php echo $tableRows;?></table>

</body>
</html>

<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013,      Geert Bergman (geert@scrivo.nl), highlight.php
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

$test = array(
	"Python" => "python",
	"Python's profiler output" => "profile",
	"Ruby" => "ruby",
	"Haml" => "haml",
	"Perl" => "perl",
	"PHP" => "php",
	"Scala" => "scala",
	"Go" => "go",
	"XML" => "xml",
	"HTML (with inline css and javascript)" => "xml",
	"Lasso" => "lasso",
	"Markdown" => "markdown",
	"AsciiDoc" => "asciidoc",
	"Django templates" => "django",
	"Handlebars" => "handlebars",
	"CSS" => "css",
	"SCSS" => "scss",
	"JSON" => "json",
	"JavaScript" => "javascript",
	"CoffeeScript" => "coffeescript",
	"ActionScript" => "actionscript",
	"VBScript" => "vbscript",
	"VB.NET" => "vbnet",
	"HTTP" => "http",
	"Lua" => "lua",
	"AppleScript" => "applescript",
	"Delphi" => "delphi",
	"Java" => "java",
	"C++" => "cpp",
	"Objective C" => "objectivec",
	"Vala" => "vala",
	"C#" => "cs",
	"F#" => "fsharp",
	"D" => "d",
	"RenderMan RSL" => "rsl",
	"RenderMan RIB" => "rib",
	"MEL (Maya Embedded Language)" => "mel",
	"GLSL" => "glsl",
	"SQL" => "sql",
	"SmallTalk" => "smalltalk",
	"Lisp" => "lisp",
	"Clojure" => "clojure",
	"Ini file" => "ini",
	"Apache" => "apache",
	"nginx" => "nginx",
	"Diff" => "diff",
	"DOS batch files" => "dos",
	"Bash" => "bash",
	"CMake" => "cmake",
	"Axapta" => "axapta",
	"Oracle Rules Language" => "ruleslanguage",
	"1С" => "1c",
	"AVR Assembler" => "avrasm",
	"VHDL" => "vhdl",
	"Parser 3" => "parser3",
	"TeX" => "tex",
	"BrainFuck" => "brainfuck",
	"Haskell" => "haskell",
	"Erlang" => "erlang",
	"Erlang REPL" => "erlang-repl",
	"Rust" => "rust",
	"Matlab" => "matlab",
	"R" => "r",
	"Mizar" => "mizar",
);

set_time_limit(60);
$start = microtime(true);

require_once("Highlight/Autoloader.php");
spl_autoload_register("Highlight\\Autoloader::load");

use Highlight\Highlighter;

$hl = new Highlighter();
$hl->setAutodetectLanguages(array_values($test));

$tableRows = "";
$failed = array();
foreach ($test as $title => $name) {
	$sn = strpos($title, "HTML") !== false ? "html" : $name;
	$snippet = file_get_contents("snippets/{$sn}.txt");
	$r = $hl->highlightAuto($snippet);
	$passed = ($r->language === $name);
	$res = "<div class=\"test\"><var class=\"".($passed?"passed":"failed")."\">{$r->language}</var>".
		" ({$r->keywordCount}+{$r->relevance}=".($r->keywordCount+$r->relevance).")<br>";
	if (isset($r->secondBest)) {
		$res .= "{$r->secondBest->language}".
			" ({$r->secondBest->keywordCount}+{$r->secondBest->relevance}=".
				($r->secondBest->keywordCount+$r->secondBest->relevance).")";
	}
	$tableRows .= "<tr><th>{$title}{$res}</th><td class=\"{$name}\">
		<pre><code class=\"{$name}\">{$r->value}</code></pre></td></th>";
	if (!$passed) {
		$failed[] = $name;
	}
}

if (count($failed)) {
	$testResult = "<p id=\"summary\" class=\"failed\">Failed tests: ".implode(", ", $failed);
} else {
	$testResult = "<p id=\"summary\" class=\"passed\">All tests passed";
}

$testResult .= "</p><p>Highlighting took ".(microtime(true)-$start)." seconds</p>"; 

?>
<!DOCTYPE html>
<head>
	<title>highlight.js test</title>
	<meta charset="utf-8">

	<link rel="stylesheet" title="Default" href="styles/default.css">
	<link rel="alternate stylesheet" title="Dark" href="styles/dark.css">
	<link rel="alternate stylesheet" title="FAR" href="styles/far.css">
	<link rel="alternate stylesheet" title="IDEA" href="styles/idea.css">
	<link rel="alternate stylesheet" title="Sunburst" href="styles/sunburst.css">
	<link rel="alternate stylesheet" title="Zenburn" href="styles/zenburn.css">
	<link rel="alternate stylesheet" title="Visual Studio" href="styles/vs.css">
	<link rel="alternate stylesheet" title="Ascetic" href="styles/ascetic.css">
	<link rel="alternate stylesheet" title="Magula" href="styles/magula.css">
	<link rel="alternate stylesheet" title="GitHub" href="styles/github.css">
	<link rel="alternate stylesheet" title="Google Code" href="styles/googlecode.css">
	<link rel="alternate stylesheet" title="Brown Paper" href="styles/brown_paper.css">
	<link rel="alternate stylesheet" title="School Book" href="styles/school_book.css">
	<link rel="alternate stylesheet" title="IR Black" href="styles/ir_black.css">
	<link rel="alternate stylesheet" title="Solarized - Dark" href="styles/solarized_dark.css">
	<link rel="alternate stylesheet" title="Solarized - Light" href="styles/solarized_light.css">
	<link rel="alternate stylesheet" title="Arta" href="styles/arta.css">
	<link rel="alternate stylesheet" title="Monokai" href="styles/monokai.css">
	<link rel="alternate stylesheet" title="Monokai Sublime" href="styles/monokai_sublime.css">
	<link rel="alternate stylesheet" title="XCode" href="styles/xcode.css">
	<link rel="alternate stylesheet" title="Pojoaque" href="styles/pojoaque.css">
	<link rel="alternate stylesheet" title="Rainbow" href="styles/rainbow.css">
	<link rel="alternate stylesheet" title="Tomorrow" href="styles/tomorrow.css">
	<link rel="alternate stylesheet" title="Tomorrow Night" href="styles/tomorrow-night.css">
	<link rel="alternate stylesheet" title="Tomorrow Night Bright" href="styles/tomorrow-night-bright.css">
	<link rel="alternate stylesheet" title="Tomorrow Night Blue" href="styles/tomorrow-night-blue.css">
	<link rel="alternate stylesheet" title="Tomorrow Night Eighties" href="styles/tomorrow-night-eighties.css">
	<link rel="alternate stylesheet" title="Railscasts" href="styles/railscasts.css">
	<link rel="alternate stylesheet" title="Obsidian" href="styles/obsidian.css">
	<link rel="alternate stylesheet" title="Docco" href="styles/docco.css">
	<link rel="alternate stylesheet" title="Mono Blue" href="styles/mono-blue.css">
	<link rel="alternate stylesheet" title="Foundation" href="styles/foundation.css">

	<style>
		body {
			font: small Arial, sans-serif;
		}
		h2 {
			font: bold 100% Arial, sans-serif;
			margin-top: 2em;
			margin-bottom: 0.5em;
		}
		table {
			width: 100%; padding: 0; border-collapse: collapse;
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
			margin: 0; font-size: medium;
		}
		#switch {
			overflow: auto; width: 67em;
			list-style: none;
			padding: 0; margin: 0;
		}
		#switch li {
			float: left; width: 12em;
			padding: 0.1em; margin: 0.1em 1em 0.1em 0;
			background: #EEE;
			cursor: pointer;
		}
		#switch li.current {
			background: #CCC;
		}
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
		.failed {
			color: red;
		}
		.code {
			font: medium monospace;
		}
		.code .keyword {
			font-weight: bold;
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
			var ul = document.createElement('ul')

			for (var i = 0; (link = links[i]); i++) {
				if (link.getAttribute('rel').indexOf('style') != -1
						&& link.title) {

					var title = link.title;

					info[title] = {
						'link': link,
						'li': document.createElement('li')
					}

					ul.appendChild(info[title].li)
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
						}})(info[title]));
				}
			}

			current = info['Default']
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
<a href="http://softwaremaniacs.org/soft/highlight/en/">highlight.js</a>.
Most snippets do not contain working code :-).

<div id="styleswitcher">
  <h2>Styles</h2>
</div>

<h2>Automatically detected languages</h2>

<?php echo $testResult;?>
<table id="autotest"><?php echo $tableRows;?></table>

</body>
</html>

<?php
/* Copyright (c)
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

/**
 * Extract language definions in JSON format from highlight.js test page.
 */

define("HIGHLIGHT_JS", "/var/www/html/highlight.js-8.0");

function echoFile($name) {
	$f = file_get_contents(HIGHLIGHT_JS."/src/languages/{$name}");
	$f = str_replace("'</script>'", "\"<\\/script>\"", $f);
	echo $f;
}

?>
<!DOCTYPE html>
<head>
  <title>highlight.js test</title>
  <meta charset="utf-8">
  <script src="//yandex.st/dojo/1.9.1/dojo/dojo.js"></script>
  <script>

require(["dojox/json/ref"], function(){


	var hljs = new <?php include HIGHLIGHT_JS."/src/highlight.js"; ?> ();
	var lang = [];

<?php

foreach (new DirectoryIterator(HIGHLIGHT_JS."/src/languages") as $fileInfo) {
	if ($fileInfo->isDot()) {
		continue;
	}
	echo "\nlang[\"".str_replace(".js", "", $fileInfo->getFilename())."\"] = ";
	echoFile($fileInfo->getFilename());
}

?>

	var refs = [];
	function regExpsRep(l,p) {
		refs.push(l);
		for (x in {"begin":1, "end":2, "lexemes":3, "illegal":4}) {
			if (l[x] && l[x].source) {
				l[x] = l[x].source;
			}
		}
		for (var i in l) {
			var doneIt = false;
			for (var j=0; j<refs.length; j++) {
				if (refs[j] == l[i]) {
					doneIt = true;
				}
			}
			if (l[i] && typeof l[i] == 'object' && !doneIt) {
				regExpsRep(l[i], l[i]);
			}
		}
	}

	function patch(o, m) {
		if (o[m]) {
			o[m] = o[m].replace("\/", "/");
			o[m] = o[m].replace("/", "\/");
		}
	}

	var res = "";
	for (var p in lang) {
		res += p + "\n";
		var l = eval("lang[\""+p+"\"](hljs)");
		refs = [];
		regExpsRep(l);
		hljs.registerLanguage(p, lang[p]);
		res += dojox.json.ref.toJson(l) + "\n";

	}

	// This is the output to capture
	console.log(res);

});

</script>

<body>

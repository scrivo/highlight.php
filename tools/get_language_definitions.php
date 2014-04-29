<?php
/**
 * Extract language definions in JSON format from highlight.js test page.
 */

define("HIGHLIGHT_JS", "/var/www/html/highlight.js-7.5");

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
//	if ($fileInfo->getFilename() != "lasso.js") continue;
	echo "\nlang[\"".str_replace(".js", "", $fileInfo->getFilename())."\"] = ";
	echoFile($fileInfo->getFilename());
}

?>

	var refs = [];
	function regExpsRep(l,p) {
		refs.push(l);
		for (x in {"begin":1, "end":2, "lexems":3, "illegal":4}) {
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
/*		
		for (i in {"begin":1, "end":2, "lexems":3, "illegal":4}) {
			patch(l, i);
		}
*/		
		hljs.LANGUAGES[p] = l;
		res += dojox.json.ref.toJson(l) + "\n";

	}

	// This is the output to capture
	console.log(res);

});

</script>

<body>

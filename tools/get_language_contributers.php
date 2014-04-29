<?php
/**
 * Extract language definions in JSON format from highlight.js test page.
 */

define("HIGHLIGHT_JS", "/var/www/html/highlight.js-7.5");

function echoFile($name) {
	$f = str_replace("'</script>'", "\"<\\/script>\"", $f);
	echo $f;
}

$files = array();
foreach (new DirectoryIterator(HIGHLIGHT_JS."/src/languages") as $fileInfo) {
	if ($fileInfo->isDot()) {
		continue;
	}
	$files[] = $fileInfo->getFilename();
}

sort($files);
	
foreach ($files as $fn) {
	$authors = array();
	$contributors = array();
	$f = file(HIGHLIGHT_JS."/src/languages/{$fn}");

	$f2 = array();
	foreach($f as $l) {
		if (preg_match("/^\s/", $l)) {
			$f2[count($f2)-1] .= ", " . trim($l);
		} else {
			$f2[] = trim($l);
		}
	}
	
	foreach($f2 as $l) {
		$la = explode(":", $l, 2);
		if (strtolower(substr(trim($la[0]), 0, 6)) == "author") {
			$authors[] = trim($la[1]);
		}
		if (strtolower(substr(trim($la[0]), 0, 11)) == "contributor") {
			$contributors[] = trim($la[1]);
		}
		
	}
	echo str_replace(".js", ".json", $fn)."\n";
	if (count($authors)) { 
		echo "\tAuthor(s): ".implode("(author) , ", $authors)."\n";
	} else {
		echo "\tAuthor(s): Ivan Sagalaev <maniac@softwaremaniacs.org>\n";
		
	}
	
	if (count($contributors)) { 
		echo "\tContributor(s): ".implode(", ", $contributors)."\n";
	}
	
	echo "\n";
	
}

?>
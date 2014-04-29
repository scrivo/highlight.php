<?php
/**
 * Extract code snippets from highlight.js test page.
 */

define("HIGHLIGHT_JS", "/var/www/html/highlight.js-7.5");
define("SNIPPETS_DIR", "../test/snippets");

$doc = new DOMDocument();
$doc->loadHTMLFile(HIGHLIGHT_JS."/src/test.html");

$lst = array();

foreach($doc->getElementById("autotest")->getElementsByTagName("tr") as $row) {

	$title = trim($row->getElementsByTagName("th")->item(0)->firstChild->data);
	$td = $row->getElementsByTagName("td")->item(0);
	$lang = $td->getAttribute("class");
	$code = html_entity_decode(
			$td->getElementsByTagName("code")->item(0)->firstChild->data);

	$lst[$title] = $lang;
	
	// patch:
	if ($lang === "xml" && strpos($title, "HTML") !== false) {
		$lang = "html";
	}
	file_put_contents(SNIPPETS_DIR."/{$lang}.txt", $code);
}

file_put_contents(SNIPPETS_DIR."/snippets.json", json_encode($lst));

?>
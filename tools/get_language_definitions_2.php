<?php 

$f = file("languages.dat");

for ($i=0; $i<count($f); $i+=2) {
	$fl = trim($f[$i]);
	file_put_contents("../Highlight/languages/{$fl}.json", $f[$i+1]);
}

?>
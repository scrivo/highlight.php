<?php
/* Copyright (c)
 * - 2013-2014, Geert Bergman (geert@scrivo.nl), highlight.php
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

/**
 * Extract language definions in JSON format from highlight.js test page.
 */

define("HIGHLIGHT_JS", "/var/www/html/highlight.js-8.1");

function echoFile($name)
{
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
    foreach ($f as $l) {
        if (preg_match("/^\s/", $l)) {
            $f2[count($f2)-1] .= ", " . trim($l);
        } else {
            $f2[] = trim($l);
        }
    }
    
    foreach ($f2 as $l) {
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

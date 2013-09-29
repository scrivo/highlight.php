<?php
/* Copyright (c)
 * - 2006-2013, Nathan Grigg (nathan@nathanamy.org), highlight.js 
 *              (original author)
 * - 2006-2013, Dr. Drang (drdrang@gmail.com), highlight.js (original author)
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

/**
 * This file is a direct port of applescript.js, the AppleScript language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class AppleScript extends Language {
	
	protected function getName() {
		return "applescript";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"about above after against and around as at back before beginning " .
				"behind below beneath beside between but by considering " .
				"contain contains continue copy div does eighth else end equal " .
				"equals error every exit fifth first for fourth from front " .
				"get given global if ignoring in into is it its last local me " .
				"middle mod my ninth not of on onto or over prop property put ref " .
				"reference repeat returning script second set seventh since " .
				"sixth some tell tenth that the then third through thru " .
				"timeout times to transaction try until where while whose with " .
				"without",
			"constant" =>
				"AppleScript false linefeed return pi quote result space tab true",
			"type" =>
				"alias application boolean class constant date file integer list " .
				"number real record string text",
			"command" =>
				"activate beep count delay launch log offset read round " .
				"run say summarize write",
			"property" =>
				"character characters contents day frontmost id item length " .
				"month name paragraph paragraphs rest reverse running time version " .
				"weekday word words year"				
		);
	}
	
	protected function getIllegal() {
		return "\/\/";
	}
	
	protected function getContainedModes() {
		
		$STRING = clone $this->QUOTE_STRING_MODE;
		$STRING->illegal = "";
		
		$TITLE = new Mode(array(
			"className" => "title", 
			"begin" => $this->UNDERSCORE_IDENT_RE
		));
		
		$PARAMS = new Mode(array(
			"className" => "params",
			"begin" => "\(", 
			"end" => "\)",
			"contains" => array("self", $this->C_NUMBER_MODE, $STRING)
		));
		
		$COMMENTS = array(
			new Mode(array(
				"className" => "comment",
				"begin" => "--", 
				"end" => "$",
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "\(\*", "end" => "\*\)",
				"contains" => array(
					"self", 
					new Mode(array(
						"begin" => "--", 
						"end" => "$"
					))
				) //allow nesting
			)),
			$this->HASH_COMMENT_MODE
		);
				
		return array_merge(
			array(
				$STRING,
				$this->C_NUMBER_MODE,
				new Mode(array(
					"className" => "type",
					"begin" => "\bPOSIX file\b"
				)),
				new Mode(array(
					"className" => "command",
					"begin" =>
						"\b(clipboard info|the clipboard|info for|list (disks|folder)|" .
						"mount volume|path to|(close|open for) access|(get|set) eof|" .
						"current date|do shell script|get volume settings|random number|" .
						"set volume|system attribute|system info|time to GMT|" .
						"(load|run|store) script|scripting components|" .
						"ASCII (character|number)|localized string|" .
						"choose (application|color|file|file name|" .
						"folder|from list|remote application|URL)|" .
						"display (alert|dialog))\b|^\s*return\b"
				)),
				new Mode(array(
					"className" => "constant",
					"begin" =>
						"\b(text item delimiters|current application|missing value)\b"
				)),
				new Mode(array(
					"className" => "keyword",
					"begin" =>
						"\b(apart from|aside from|instead of|out of|greater than|" .
						"isn't|(doesn't|does not) (equal|come before|come after|contain)|" .
						"(greater|less) than( or equal)?|(starts?|ends|begins?) with|" .
						"contained by|comes (before|after)|a (ref|reference))\b"
				)),
				new Mode(array(
					"className" => "property",
					"begin" =>
						"\b(POSIX path|(date|time) string|quoted form)\b"
				)),
				new Mode(array(
					"className" => "function_start",
					"beginWithKeyword" => true,
					"keywords" => "on",
					"illegal" => "[\${=;\n]",
					"contains" => array($TITLE, $PARAMS)
				))
			),
			$COMMENTS
		);
		
	}

}

?>
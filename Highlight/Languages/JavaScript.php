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

/**
 * This file is a direct port of javascript.js, the JavaScript language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class JavaScript extends Language {
	
	protected function getName() {
		return "javascript";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>  
				"in if for while finally var new function do return void else break catch " .
				"instanceof with throw case default try this switch continue typeof delete " .
				"let yield const",
			"literal" =>  
				"true false null undefined NaN Infinity"
		);
	}

	protected function getContainedModes() {
	
		return array(
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$this->C_NUMBER_MODE,
			new Mode(array( // "value" container
				"begin" => "(" . $this->RE_STARTERS_RE . "|\b(case|return|throw)\b)\s*",
				"keywords" => "return throw case",
				"contains" => array(
					$this->C_LINE_COMMENT_MODE,
					$this->C_BLOCK_COMMENT_MODE,
					$this->REGEXP_MODE,
					new Mode(array(
						"begin" => "<", 
						"end" => ">;",
						"subLanguage" => "xml"
					))
				),
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "function",
				"beginWithKeyword" => true, 
				"end" => "{",
				"keywords" => "function",
				"contains" => array(
					new Mode(array(
						"className" => "title", 
						"begin" => "[A-Za-z\$_][0-9A-Za-z\$_]*"
					)),
					new Mode(array(
						"className" => "params",
						"begin" => "\(", 
						"end" => "\)",
						"contains" => array(
							$this->C_LINE_COMMENT_MODE,
							$this->C_BLOCK_COMMENT_MODE
						),
						"illegal" => "[\"'(]"
					))
				),
				"illegal" => "\[|%"
			))
		);
	}

}

?>
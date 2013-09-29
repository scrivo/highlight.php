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
 * This file is a direct port of python.js, the Python language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Python extends Language {
	
	protected function getName() {
		return "python";
	}
	
	protected function getIllegal() {
		return "(<\/|->|\?)";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"and elif is global as in if from raise for except finally print import pass return " .
				"exec else break not with class assert yield try while continue del or def lambda " .
				"nonlocal|10",
			"built_in" =>
				"None True False Ellipsis NotImplemented"
		);
	}

	protected function getContainedModes() {
		
		$PROMPT = new Mode(array(
			"className" => "prompt",  
			"begin" => "^(>>>|\.\.\.) "
		));
		
		$STRINGS = array(
			new Mode(array(
				"className" => "string",
				"begin" => "(u|b)?r?'''", 
				"end" => "'''",
				"contains" => array($PROMPT),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "(u|b)?r?\"\"\"", 
				"end" => "\"\"\"",
				"contains" => array($PROMPT),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "(u|r|ur)'", 
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "(u|r|ur)\"", 
				"end" => "\"",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "(b|br)'", 
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "(b|br)\"", 
				"end" => "\"",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE
		);
			
		$TITLE = new Mode(array(
			"className" => "title", 
			"begin" => $this->UNDERSCORE_IDENT_RE
		));
		
		$PARAMS = new Mode(array(
			"className" => "params",
			"begin" => "\(", 
			"end" => "\)",
			"contains" => array_merge(
				array(
					"self", 
					$this->C_NUMBER_MODE, 
					$PROMPT
				),
				$STRINGS
			)
		));
		
		$CLASS_PROTO = new Mode(array(
			"className" => "class",
			"keywords" => "class",
			"beginWithKeyword" => true, 
			"end" => ":",
			"illegal" => "[\${=;\n]",
			"contains" => array($TITLE, $PARAMS),
			"relevance" => 10
		));
		
		$FUNC_PROTO = clone $CLASS_PROTO;
		$FUNC_PROTO->className = "function";
		$FUNC_PROTO->keywords = "def";
		
		return array_merge(
			$STRINGS,
			array(
				$PROMPT,
				$this->HASH_COMMENT_MODE,
				$FUNC_PROTO,
				$CLASS_PROTO,
				$this->C_NUMBER_MODE,
				new Mode(array(
					"className" => "decorator",
					"begin" => "@", 
					"end" => "$"
				)),
				new Mode(array(
					"begin" => "\b(print|exec)\(" // don’t highlight keywords-turned-functions in Python 3
				))
			)
		);

	}

}

?>
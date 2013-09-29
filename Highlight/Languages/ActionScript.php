<?php
/* Copyright (c)
 * - 2006-2013, Alexander Myadzel (myadzel@gmail.com), highlight.js
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
 * This file is a direct port of actionscript.js, the ActionScript language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class ActionScript extends Language {
	
	protected function getName() {
		return "actionscript";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"as break case catch class const continue default delete do dynamic each " .
				"else extends final finally for function get if implements import in include " .
				"instanceof interface internal is namespace native new override package private " .
				"protected public return set static super switch this throw try typeof use var void " .
				"while with",
			"literal" =>
				"true false null undefined"
		);
	}

	protected function getContainedModes() {
		
		$IDENT_RE = "[a-zA-Z_$][a-zA-Z0-9_$]*";
		$IDENT_FUNC_RETURN_TYPE_RE = "([*]|[a-zA-Z_$][a-zA-Z0-9_$]*)";
		
		$AS3_REST_ARG_MODE = new Mode(array(
			"className" => "rest_arg",
			"begin" => "[.]{3}", 
			"end" => $IDENT_RE,
			"relevance" => 10
		));
		$TITLE_MODE = new Mode(array(
			"className" => "title", 
			"begin" => $IDENT_RE
		));
	
		return array(
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "package",
				"beginWithKeyword" => true, 
				"end" => "{",
				"keywords" => "package",
				"contains" => array($TITLE_MODE)
			)),
			new Mode(array(
				"className" => "class",
				"beginWithKeyword" => true, 
				"end" => "{",
				"keywords" => "class interface",
				"contains" => array(
					new Mode(array(
						"beginWithKeyword" => true,
						"keywords" => "extends implements"
					)),
					$TITLE_MODE
				)
			)),
			new Mode(array(
				"className" => "preprocessor",
				"beginWithKeyword" => true, 
				"end" => ";",
				"keywords" => "import include"
			)),
			new Mode(array(
				"className" => "function",
				"beginWithKeyword" => true, 
				"end" => "[{;]",
				"keywords" => "function",
				"illegal" => "\S",
				"contains" => array(
					$TITLE_MODE,
					new Mode(array(
						"className" => "params",
						"begin" => "\(", 
						"end" => "\)",
						"contains" => array(
							$this->APOS_STRING_MODE,
							$this->QUOTE_STRING_MODE,
							$this->C_LINE_COMMENT_MODE,
							$this->C_BLOCK_COMMENT_MODE,
							$AS3_REST_ARG_MODE
						)
					)),
					new Mode(array(
						"className" => "type",
						"begin" => ":",
						"end" => $IDENT_FUNC_RETURN_TYPE_RE,
						"relevance" => 10
					))
				)
			))
				
		);
	}

}

?>
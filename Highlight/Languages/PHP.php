<?php
/* Copyright (c)
 * - 2006-2013, Victor Karamzin (Victor.Karamzin@enterra-inc.com), highlight.js
 *              (original author)
 * - 2006-2013, Evgeny Stepanischev (imbolk@gmail.com), highlight.js
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
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
 * This file is a direct port of php.js, the PHP language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class PHP extends Language {
	
	protected function getName() {
		return "php";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return 
			"and include_once list abstract global private echo interface as static endswitch " .
			"array null if endwhile or const for endforeach self var while isset public " .
			"protected exit foreach throw elseif include __FILE__ empty require_once do xor " .
			"return implements parent clone use __CLASS__ __LINE__ else break print eval new " .
			"catch __METHOD__ case exception php_user_filter default die require __FUNCTION__ " .
			"enddeclare final try this switch continue endfor endif declare unset true false " .
			"namespace trait goto instanceof insteadof __DIR__ __NAMESPACE__ __halt_compiler";
	}

	protected function getContainedModes() {
	
		$VARIABLE = new Mode(array(
			"className" => "variable", 
			"begin" => "\\$+[a-zA-Z_\p{L}][a-zA-Z0-9_\p{L}\p{N}]*"
		));
		
		$tmp1 = $this->APOS_STRING_MODE;
		$tmp1->illegal = null;
		$tmp2 = $this->QUOTE_STRING_MODE;
		$tmp2->illegal = null;
		$STRINGS = array(
			$tmp1,
			$tmp2,
			new Mode(array(
				"className" => "string",
				"begin" => "b\"", // binary string PHP 6
				"end" => "\"",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "b'", // binary string PHP 6
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
		);
		
		$NUMBERS = array(
			$this->BINARY_NUMBER_MODE, 
			$this->C_NUMBER_MODE
		);
		
		$TITLE = new Mode(array(
			"className"=> "title", 
			"begin" => $this->UNDERSCORE_IDENT_RE
		));	

		return array_merge(
			array(
				$this->C_LINE_COMMENT_MODE,
				$this->HASH_COMMENT_MODE,
				new Mode(array(
					"className" => "comment",
					"begin" => "\/\*", 
					"end" => "\*\/",
					"contains" => array(
						new Mode(array(
							"className" => "phpdoc",
							"begin" => "\s@[A-Za-z]+"
						))
					)
				)),
				new Mode(array(
					"className" => "comment",
					"excludeBegin" => true,
					"begin" => "__halt_compiler.+?;", 
					"endsWithParent" => true
				)),
				new Mode(array(
					"className" => "string",
					"begin" => "<<<['\"]?\\w+['\"]?$", 
					"end" => "^\\w+;",
					"contains" => array($this->BACKSLASH_ESCAPE)
				)),
				new Mode(array(
					"className" => "preprocessor",
					"begin" => "<\?php",
					"relevance" => 10
				)),
				new Mode(array(
					"className" => "preprocessor",
					"begin" => "\?>"
				)),
				$VARIABLE,
				new Mode(array(
					"className" => "function",
					"beginWithKeyword" => true, 
					"end" => "{",
					"keywords" => "function",
					"illegal" => "\\$|\[|%",
					"contains" => array(
						$TITLE,
						new Mode(array(
							"className" => "params",
							"begin" => "\(", 
							"end" => "\)",
							"contains" => array_merge(
								array(
									"self",
									$VARIABLE,
									$this->C_BLOCK_COMMENT_MODE
								), 
								$STRINGS, 
								$NUMBERS
							)
						))
					)
				)),
			), 
			array(
				new Mode(array(
					"className" => "class",
					"beginWithKeyword" => true, 
					"end" => "{",
					"keywords" => "class",
					"illegal" => "[:\(\$]",
					"contains" => array(
						new Mode(array(
							"beginWithKeyword" => true, 
							"endsWithParent" => true,
							"keywords" => "extends",
							"contains" => array($TITLE)
						)),
						$TITLE
					)
				)),
				new Mode(array(
					"begin" => "=>" // No markup, just a relevance booster
				))
			), 
			$STRINGS, 
			$NUMBERS
		);

	}

}

?>
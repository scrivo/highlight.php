<?php
/* Copyright (c)
 * - 2006-2013, Dmytrii Nagirniak (dnagir@gmail.com), highlight.js
 *              (original author)
 * - 2006-2013, Oleg Efimov (efimovov@gmail.com), highlight.js
 * - 2006-2013, Cédric Néhémie (cedric.nehemie@gmail.com), highlight.js
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
 * $SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * This file is a direct port of coffeescript.js, the CoffeeScript language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class CoffeeScript extends Language {
	
	protected function getName() {
		return "coffeescript";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				// JS keywords
				"in if for while finally new do return else break catch instanceof throw try this " .
				"switch continue typeof delete debugger super " .
				// Coffee keywords
				"then unless until loop of by when and or is isnt not",
			"literal" =>
				// JS literals
				"true false null undefined " .
				// Coffee literals
				"yes no on off",
			"reserved" =>
				"case default function var void with const let enum export import native " .
				"__hasProp __extends __slice __bind __indexOf",
			"built_in" =>
				"npm require console print module exports global window document"
		);
	}

	protected function getContainedModes() {
		
		$JS_IDENT_RE = "[A-Za-z\$_][0-9A-Za-z\$_]*";
		
		$TITLE = new Mode(array(
			"className" => "title", 
			"begin" => $JS_IDENT_RE
		));
		
		$SUBST = new Mode(array(
			"className" => "subst",
			"begin" => "#\{", "end" => "}",
			"keywords" => $this->getKeyWords(),
		));
		
		// a number tries to eat the following slash to prevent treating it as a regexp
		$TMP = clone $this->C_NUMBER_MODE;
		$TMP->starts = new Mode(
			array("end" => "(\s*\/)?", 
			"relevance" => 0
		));
		
		$EXPRESSIONS = array(
			// Numbers
			$this->BINARY_NUMBER_MODE,
			$TMP, 
			// Strings
			new Mode(array(
				"className" => "string",
				"begin" => "'''", 
				"end" => "'''",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "'", 
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"\"\"", 
				"end" => "\"\"\"",
				"contains" => array($this->BACKSLASH_ESCAPE, $SUBST)
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"", 
				"end" => "\"",
				"contains" => array($this->BACKSLASH_ESCAPE, $SUBST),
				"relevance" => 0
			)),
			// RegExps
			new Mode(array(
				"className" => "regexp",
				"begin" => "\/\/\/", 
				"end" => "\/\/\/",
				"contains" => array($this->HASH_COMMENT_MODE)
			)),
			new Mode(array(
				"className" => "regexp", 
				"begin" => "\/\/[gim]*",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "regexp",
				"begin" => "\/\S(\\.|[^\n])*?\/[gim]*(?=\s|\W|$)" 
					// \S is required to parse x / 2 / 3 as two divisions
			)),
			new Mode(array(
				"className" => "property",
				"begin" => "@" . $JS_IDENT_RE
			)),
			new Mode(array(
				"begin" => "`", "end" => "`",
				"excludeBegin" => true, 
				"excludeEnd" => true,
				"subLanguage" => "javascript"
			))
		);
		
		$SUBST->contains = $EXPRESSIONS;
		
		return array_merge(
			$EXPRESSIONS,
			array(
				new Mode(array(
					"className" => "comment",
					"begin" => "###",
					 "end" => "###"
				)),
				$this->HASH_COMMENT_MODE,
				new Mode(array(
					"className" => "function",
					"begin" => "(" . $JS_IDENT_RE . "\s*=\s*)?(\(.*\))?\s*[-=]>", 
					"end" => "[-=]>",
					"returnBegin" => true,
					"contains" => array(
						$TITLE,
						new Mode(array(
							"className" => "params",
							"begin" => "\(", 
							"returnBegin" => true,
							/* We need another contained nameless mode to not have every nested
							 pair of parens to be called "params" */
							"contains" => array(
								new Mode(array(
									"begin" => "\(", 
									"end" => "\)",
									"keywords" => $this->getKeyWords(),
									"contains" => array_merge(
										array("self"),
										$EXPRESSIONS
									)
								))
							)
						))
					)
				)),
				new Mode(array(
					"className" => "class",
					"beginWithKeyword" => true, 
					"keywords" => "class",
					"end" => "$",
					"illegal" => "[:\[\]]",
					"contains" => array(
						new Mode(array(
							"beginWithKeyword" => true, 
							"keywords" => "extends",
							"endsWithParent" => true,
							"illegal" => ":",
							"contains" => array($TITLE)
						)),
						$TITLE
					)
				)),
				new Mode(array(
					"className" => "attribute",
					"begin" => $JS_IDENT_RE . ":", 
					"end" => ":",
					"returnBegin" => true, 
					"excludeEnd" => true
				))
			)
		);
			
	}

}

?>
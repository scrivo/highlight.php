<?php
/* Copyright (c)
 * - 2006-2013, Nikolay Zakharov (nikolay.desh@gmail.com), highlight.js
 *              (original author)
 * - 2006-2013, Dmitry Kovega (arhibot@gmail.com), highlight.js
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
 * This file is a direct port of erlang.js, the Erlang language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Erlang extends Language {
	
	protected function getName() {
		return "erlang";
	}
	
	protected function getIllegal() {
		return "(<\/|\*=|\+=|-=|\/=|\/\*|\*\/|\(\*|\*\))";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"after and andalso|10 band begin bnot bor bsl bzr bxor case catch cond div end fun let " .
				"not of orelse|10 query receive rem try when xor",
			"literal" =>
				"false true"
		);
	}

	protected function getContainedModes() {
		
		$BASIC_ATOM_RE = "[a-z'][a-zA-Z0-9_']*";
		$FUNCTION_NAME_RE = "(" . $BASIC_ATOM_RE . ":" . $BASIC_ATOM_RE . "|" . $BASIC_ATOM_RE . ")";
		
		$COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => "%", 
			"end" => "$",
			"relevance" => 0
		));
		
		$NUMBER = new Mode(array(
			"className" => "number",
			"begin" => "\b(\d+#[a-fA-F0-9]+|\d+(\.\d+)?([eE][-+]?\d+)?)",
			"relevance" => 0
		));
		
		$NAMED_FUN = new Mode(array(
			"begin" => "fun\s+" . $BASIC_ATOM_RE . "\/\d+"
		));
		
		$FUNCTION_CALL = new Mode(array(
			"begin" => $FUNCTION_NAME_RE . "\(", 
			"end" => "\)",
			"returnBegin" => true,
			"relevance" => 0,
			"contains" => array(
				new Mode(array(
					"className" => "function_name", 
					"begin" => $FUNCTION_NAME_RE,
					"relevance" => 0
				)),
				new Mode(array(
					"begin" => "\(", 
					"end" => "\)", 
					"endsWithParent" => true,
					"returnEnd" => true,
					"relevance" => 0
					// "contains" defined later
				))
			)
		));
		
		$TUPLE = new Mode(array(
			"className" => "tuple",
			"begin" => "{", 
			"end" => "}",
			"relevance" => 0
			// "contains" defined later
		));
		
		$VAR1 = new Mode(array(
			"className" => "variable",
			"begin" => "\b_([A-Z][A-Za-z0-9_]*)?",
			"relevance" => 0
		));
		
		$VAR2 = new Mode(array(
			"className" => "variable",
			"begin" => "[A-Z][a-zA-Z0-9_]*",
			"relevance" => 0
		));
		
		$RECORD_ACCESS = new Mode(array(
			"begin" => "#", 
			"end" => "}",
			"illegal" => ".",
			"relevance" => 0,
			"returnBegin" => true,
			"contains" => array(
			new Mode(array(
				"className" => "record_name",
				"begin" => "#" . $this->UNDERSCORE_IDENT_RE,
				"relevance" => 0
			)),
			new Mode(array(
				"begin" => "{", 
				"endsWithParent" => true,
				"relevance" => 0
				// "contains" defined later
			))
			)
		));
		
		$BLOCK_STATEMENTS = new Mode(array(
			"keywords" => $this->getKeyWords(),
			"begin" => "(fun|receive|if|try|case)", 
			"end" => "end"
		));
		
		$tmp = clone $this->APOS_STRING_MODE;
		$tmp->className = "";
		
		$BLOCK_STATEMENTS->contains = array(
			$COMMENT,
			$NAMED_FUN,
			$tmp,
			$BLOCK_STATEMENTS,
			$FUNCTION_CALL,
			$this->QUOTE_STRING_MODE,
			$NUMBER,
			$TUPLE,
			$VAR1, 
			$VAR2,
			$RECORD_ACCESS
		);
		
		$BASIC_MODES = array(
			$COMMENT,
			$NAMED_FUN,
			$BLOCK_STATEMENTS,
			$FUNCTION_CALL,
			$this->QUOTE_STRING_MODE,
			$NUMBER,
			$TUPLE,
			$VAR1, 
			$VAR2,
			$RECORD_ACCESS
		);
		
		$FUNCTION_CALL->contains[1]->contains = $BASIC_MODES;
		$TUPLE->contains = $BASIC_MODES;
		$RECORD_ACCESS->contains[1]->contains = $BASIC_MODES;
		
		$PARAMS = new Mode(array(
			"className" => "params",
			"begin" => "\(", 
			"end" => "\)",
			"contains" => $BASIC_MODES
		));
		
		return array(
			new Mode(array(
				"className" => "function",
				"begin" => "^" . $BASIC_ATOM_RE . "\s*\(", 
				"end" => "->",
				"returnBegin" => true,
				"illegal" => "\(|#|\/\/|\/\\*|\\\\|:",
				"contains" => array(
					$PARAMS,
					new Mode(array(
						"className" => "title", 
						"begin" => $BASIC_ATOM_RE
					))
				),
				"starts" => new Mode(array(
					"end" => ";|\.",
					"keywords" => $this->getKeyWords(),
					"contains" => $BASIC_MODES
				))
			)),
			$COMMENT,
			new Mode(array(
				"className" => "pp",
				"begin" => "^-", 
				"end" => "\.",
				"relevance" => 0,
				"excludeEnd" => true,
				"returnBegin" => true,
				"lexems" => "-" . $this->IDENT_RE,
				"keywords" =>
					"-module -record -undef -export -ifdef -ifndef -author -copyright -doc -vsn " .
					"-import -include -include_lib -compile -define -else -endif -file -behaviour " .
					"-behavior",
				"contains" => array($PARAMS)
			)),
			$NUMBER,
			$this->QUOTE_STRING_MODE,
			$RECORD_ACCESS,
			$VAR1, 
			$VAR2,
			$TUPLE
		);
	}

}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Vasily Polovnyov (vast@whiteants.net), highlight.js
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
 * This file is a direct port of lisp.js, the Lisp language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Lisp extends Language {
	
	protected function getName() {
		return "lisp";
	}
	
	protected function getKeyWords() {
		return null; 
	}

	protected function getIllegal() {
		return "[^\s]";
	}
	
	protected function getContainedModes() {
		
		$LISP_IDENT_RE = 
			"[a-zA-Z_\-\+\*\/\<\=\>\&\#][a-zA-Z0-9_\-\+\*\/\<\=\>\&\#!]*";
		$LISP_SIMPLE_NUMBER_RE = 
			"(\-|\+)?\d+(\.\d+|\/\d+)?((d|e|f|l|s)(\+|\-)?\d+)?";

		$SHEBANG = new Mode(array(
			"className" => "shebang",
			"begin" => "^#!", 
			"end" => "$"
		));
		
		$LITERAL = new Mode(array(
			"className" => "literal",
			"begin" => "\b(t{1}|nil)\b"
		));
		
		$NUMBERS = array(
			new Mode(array(
				"className" => "number", 
				"begin" => $LISP_SIMPLE_NUMBER_RE
			)),
			new Mode(array(
				"className" => "number", 
				"begin" => "#b[0-1]+(\/[0-1]+)?"
			)),
			new Mode(array(
				"className" => "number", 
				"begin" => "#o[0-7]+(\/[0-7]+)?"
			)),
			new Mode(array(
				"className" => "number", 
				"begin" => "#x[0-9a-f]+(\/[0-9a-f]+)?"
			)),
			new Mode(array(
				"className" => "number", 
				"begin" => "#c\(" . $LISP_SIMPLE_NUMBER_RE . 
					" +" . $LISP_SIMPLE_NUMBER_RE, 
				"end" => "\)"
			))
		);
		
		$STRING = new Mode(array(
			"className" => "string",
			"begin" => "\"", 
			"end" => "\"",
			"contains" => array($this->BACKSLASH_ESCAPE),
			"relevance" => 0
		));
		
		$COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => ";", 
			"end" => "$"
		));
		
		$VARIABLE = new Mode(array(
			"className" => "variable",
			"begin" => "\*", 
			"end" => "\*"
		));
		
		$KEYWORD = new Mode(array(
			"className" => "keyword",
			"begin" => "[:&]" . $LISP_IDENT_RE
		));
		
		$QUOTED_LIST = new Mode(array(
			"begin" => "\(", 
			"end" => "\)",
			"contains" => array_merge(
				array("self", $LITERAL, $STRING), $NUMBERS)
		));
		
		$QUOTED1 = new Mode(array(
			"className" => "quoted",
			"begin" => "['`]\(", 
			"end" => "\)",
			"contains" => array_merge(
				$NUMBERS, array($STRING, $VARIABLE, $KEYWORD, $QUOTED_LIST))
		));
		
		$QUOTED2 = new Mode(array(
			"className" => "quoted",
			"begin" => "\(quote ", 
			"end" => "\)",
			"keywords" => "quote",
			"contains" => array_merge(
				$NUMBERS, array($STRING, $VARIABLE, $KEYWORD, $QUOTED_LIST))
		));
		
		$LIST = new Mode(array(
			"className" => "list",
			"begin" => "\(", 
			"end" => "\)"
		));
		
		$BODY = new Mode(array(
			"endsWithParent" => true,
			"relevance" => 0
		));
		
		$LIST->contains = array(
			new Mode(array(
				"className" => "title", 
				"begin" => $LISP_IDENT_RE
			)),
			$BODY 
		);
		
		$BODY->contains = array_merge( 
			array(
				$QUOTED1, 
				$QUOTED2, 
				$LIST, 
				$LITERAL
			),
			$NUMBERS,
			array(
				$STRING, 
				$COMMENT, 
				$VARIABLE, 
				$KEYWORD
			)
		);
		
		return array_merge(
			$NUMBERS,
			array(
				$SHEBANG,
				$LITERAL,
				$STRING,
				$COMMENT,
				$QUOTED1, 
				$QUOTED2,
				$LIST
			)
		);
		
		
	}

}

?>
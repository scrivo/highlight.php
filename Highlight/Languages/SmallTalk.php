<?php
/* Copyright (c)
 * - 2006-2013, Vladimir Gubarkov (xonixx@gmail.com), highlight.js
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
 * This file is a direct port of smalltalk.js, the SmallTalk language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class SmallTalk extends Language {
	
	protected function getName() {
		return "smalltalk";
	}
	
	protected function getKeyWords() {
		return "self super nil true false thisContext"; // only 6; 
	}

	protected function getContainedModes() {
		
		$VAR_IDENT_RE = "[a-z][a-zA-Z0-9_]*";
		
		$CHAR = new Mode(array(
			"className" => "char",
			"begin" => "\\$.{1}"
		));
		
		$SYMBOL = new Mode(array(
			"className" => "symbol",
			"begin" => "#" . $this->UNDERSCORE_IDENT_RE
		));
		
		return array(
			new Mode(array(
				"className" => "comment",
				"begin" => "\"", 
				"end" => "\"",
				"relevance" => 0
			)),
			$this->APOS_STRING_MODE,
			new Mode(array(
				"className" => "class",
				"begin" => "\b[A-Z][A-Za-z0-9_]*",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "method",
				"begin" => $VAR_IDENT_RE . ":"
			)),
			$this->C_NUMBER_MODE,
			$SYMBOL,
			$CHAR,
			new Mode(array(
				"className" => "localvars",
				// This looks more complicated than needed to avoid combinatorial
				// explosion under V8. It effectively means `| var1 var2 ... |` with
				// whitespace adjacent to `|` being optional.
				"begin" => "\|\s*" . $VAR_IDENT_RE . "(\s+" . $VAR_IDENT_RE . ")*\s*\|"
			)),
			new Mode(array(
				"className" => "array",
				"begin" => "\#\(", 
				"end" => "\)",
				"contains" => array(
					$this->APOS_STRING_MODE,
					$CHAR,
					$this->C_NUMBER_MODE,
					$SYMBOL
				)
			))
		);
		
	}

}

?>
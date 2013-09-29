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
 * This file is a direct port of delphi.js, the Delphi language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Delphi extends Language {
	
	protected function getName() {
		return "delphi";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return 
			"and safecall cdecl then string exports library not pascal set " .
			"virtual file in array label packed end. index while const raise for to implementation " .
			"with except overload destructor downto finally program exit unit inherited override if " .
			"type until function do begin repeat goto nil far initialization object else var uses " .
			"external resourcestring interface end finalization class asm mod case on shr shl of " .
			"register xorwrite threadvar try record near stored constructor stdcall inline div out or " .
			"procedure";
	}

	protected function getClassKeyWords() {
		return
			"safecall stdcall pascal stored const implementation " .
			"finalization except to finally program inherited override then exports string read not " .
			"mod shr try div shl set library message packed index for near overload label downto exit " .
			"public goto interface asm on of constructor or private array unit raise destructor var " .
			"type until function else external with case default record while protected property " .
			"procedure published and cdecl do threadvar file in if end virtual write far out begin " .
			"repeat nil initialization object uses resourcestring class register xorwrite inline static";
	}
	
	protected function getIllegal() {
		return "(\"|\$[G-Zg-z]|\/\*|<\/)";
	}
	
	protected function getContainedModes() {
		
		$CURLY_COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => "{", 
			"end" => "}",
			"relevance" => 0
		));
		
		$PAREN_COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => "\(\*", 
			"end" => "\*\)",
			"relevance" => 10
		));
		
		$STRING = new Mode(array(
			"className" => "string",
			"begin" => "'", 
			"end" => "'",
			"contains" => array(
				new Mode(array(
					"begin" => "''"
				))
			),
			"relevance" => 0
		));
		
		$CHAR_STRING = new Mode(array(
			"className" => "string", 
			"begin" => "(#\d+)+"
		));
		
		$FUNCTION = new Mode(array(
			"className" => "function",
			"beginWithKeyword" => true, 
			"end" => "[:;]",
			"keywords" => "function constructor|10 destructor|10 procedure|10",
			"contains" => array(
				new Mode(array(
					"className" => "title", 
					"begin" => $this->IDENT_RE
				)),
				new Mode(array(
					"className" => "params",
					"begin" => "\(", 
					"end" => "\)",
					"keywords" => $this->getKeyWords(),
					"contains" => array($STRING, $CHAR_STRING)
				)),
				$CURLY_COMMENT, 
				$PAREN_COMMENT
			)
		));
		
		return array(
			$CURLY_COMMENT, 
			$PAREN_COMMENT, 
			$this->C_LINE_COMMENT_MODE,
			$STRING, 
			$CHAR_STRING,
			$this->NUMBER_MODE,
			$FUNCTION,
			new Mode(array(
				"className" => "class",
				"begin" => "=\bclass\b",
				"end" => "end;",
				"keywords" => $this->getClassKeyWords(),
				"contains" => array(
					$STRING, 
					$CHAR_STRING,
					$CURLY_COMMENT, 
					$PAREN_COMMENT, 
					$this->C_LINE_COMMENT_MODE,
					$FUNCTION
				)
			))
		);
		
	}

}

?>
<?php
/* Copyright (c)
 * - 2006-2013, vah (vahtenberg@gmail.com), highlight.js
 *              (original author)
 * - 2006-2013, Benjamin Pannell (contact@sierrasoftworks.com), highlight.js
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
 * This file is a direct port of bash.js, the Bash language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Bash extends Language {
	
	protected function getName() {
		return "bash";
	}
	
	protected function getLexems() {
		return "-?[a-z]+";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" => 
				"if then else elif fi for break continue while in do done exit return set " .
				"declare case esac export exec",
			"literal" =>
				"true false",
			"built_in" =>
				"printf echo read cd pwd pushd popd dirs let eval unset typeset readonly " .
				"getopts source shopt caller type hash bind help sudo",
			"operator" =>
				"-ne -eq -lt -gt -f -d -e -s -l -a" // relevance booster
		);
	}

	protected function getContainedModes() {
		
		$VAR1 = new Mode(array(
			"className" => "variable", 
			"begin" => "\\$[\w\d#@][\w\d_]*"
		));
		
		$VAR2 = new Mode(array(
			"className" => "variable", 
			"begin" => "\\$\{(.*?)\}"
		));
		
		$QUOTE_STRING = new Mode(array(
			"className" => "string",
			"begin" => "\"", 
			"end" => "\"",
			"contains" => array(
				$this->BACKSLASH_ESCAPE,
				$VAR1,
				$VAR2,
				new Mode(array(
					"className" => "variable",
					"begin" => "\\$\(", 
					"end" => "\)",
					"contains" => array($this->BACKSLASH_ESCAPE)
				))
			),
			"relevance" => 0
		));
		
		$APOS_STRING = new Mode(array(
			"className" => "string",
			"begin" => "\'", 
			"end" => "\'",
			"relevance" => 0
		));
		
		return array(
			new Mode(array(
				"className" => "shebang",
				"begin" => "^#![^\n]+sh\s*$",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "function",
				"begin" => "\w[\w\d_]*\s*\(\s*\)\s*\{",
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						"className" => "title", 
						"begin" => "\w[\w\d_]*"
					))
				),
				"relevance" => 0
			)),
			$this->HASH_COMMENT_MODE,
			$this->NUMBER_MODE,
			$QUOTE_STRING,
			$APOS_STRING,
			$VAR1,
			$VAR2				
		);
		
	}

}

?>
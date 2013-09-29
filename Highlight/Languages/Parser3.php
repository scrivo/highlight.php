<?php
/* Copyright (c)
 * - 2006-2013, Oleg Volchkov (oleg@volchkov.net), highlight.js
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
 * This file is a direct port of parser3.js, the Parser 3 language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Parser3 extends Language {
	
	protected function getName() {
		return "parser3";
	}
	
	protected function getKeyWords() {
		return null;
	}

	protected function getSubLanguage() {
		return new Mode(array(
			"subLanguage" => "xml",
			"relevance" => 0
		));
	}
	
	protected function getContainedModes() {
				
		return array(
			new Mode(array(
				"className" => "comment",
				"begin" => "^#", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "\^rem{", 
				"end" => "}",
				"relevance" => 10,
				"contains" => array(
					new Mode(array(
						"begin" => "{", 
						"end" => "}",
						"contains" => array("self")
					))
				)
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "^@(?:BASE|USE|CLASS|OPTIONS)$",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "title",
				"begin" => "@[\w\-]+\[[\w^;\-]*\](?:\[[\w^;\-]*\])?(?:.*)$"
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "\\$\{?[\w\-\.\:]+\}?"
			)),
			new Mode(array(
				"className" => "keyword",
				"begin" => "\^[\w\-\.\:]+"
			)),
			new Mode(array(
				"className" => "number",
				"begin" => "\^#[0-9a-fA-F]+"
			)),
			$this->C_NUMBER_MODE
		);
		
	}

}

?>
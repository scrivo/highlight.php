<?php
/* Copyright (c)
 * - 2006-2013, Joe Cheng (joe@rstudio.org), highlight.js (original author)
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
 * This file is a direct port of r.js, the R language definition file for 
 * highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class R extends Language {
	
	protected function getName() {
		return "r";
	}
	
	protected function getKeyWords() {
		return null;
	}

	protected function getContainedModes() {
		
		$IDENT_RE = "([a-zA-Z]|\.[a-zA-Z.])[a-zA-Z0-9._]*";
		
		return array(
			$this->HASH_COMMENT_MODE,
			new Mode(array(
				"begin" => $IDENT_RE,
				"lexems" => $IDENT_RE,
				"keywords" => array(
					"keyword" =>
						"function if in break next repeat else for return switch while try tryCatch|10 " .
						"stop warning require library attach detach source setMethod setGeneric " .
						"setGroupGeneric setClass ...|10",
					"literal" =>
						"NULL NA TRUE FALSE T F Inf NaN NA_integer_|10 NA_real_|10 NA_character_|10 " .
						"NA_complex_|10"
				),
				"relevance" => 0
			)),
			new Mode(array(
				// hex value
				"className" => "number",
				"begin" => "0[xX][0-9a-fA-F]+[Li]?\b",
				"relevance" => 0
			)),
			new Mode(array(
				// explicit integer
				"className" => "number",
				"begin" => "\d+(?:[eE][+\-]?\d*)?L\b",
				"relevance" => 0
			)),
			new Mode(array(
				// number with trailing decimal
				"className" => "number",
				"begin" => "\d+\.(?!\d)(?:i\b)?",
				"relevance" => 0
			)),
			new Mode(array(
				// number
				"className" => "number",
				"begin" => "\d+(?:\.\d*)?(?:[eE][+\-]?\d*)?i?\b",
				"relevance" => 0
			)),
			new Mode(array(
				// number with leading decimal
				"className" => "number",
				"begin" => "\.\d+(?:[eE][+\-]?\d*)?i?\b",
				"relevance" => 0
			)),
			new Mode(array(
				// escaped identifier
				"begin" => "`",
				"end" => "`",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"",
				"end" => "\"",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "'",
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 0
			))
		);
		
	}

}

?>
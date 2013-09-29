<?php
/* Copyright (c)
 * - 2006-2013, Jason Diamond (jason@diamond.name), highlight.js
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
 * This file is a direct port of cs.js, the C# language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class CSharp extends Language {
	
	protected function getName() {
		return "cs";
	}
	
	protected function getKeyWords() {
		return 
			// Normal keywords.
			"abstract as base bool break byte case catch char checked class const continue decimal " .
			"default delegate do double else enum event explicit extern false finally fixed float " .
			"for foreach goto if implicit in int interface internal is lock long namespace new null " .
			"object operator out override params private protected public readonly ref return sbyte " .
			"sealed short sizeof stackalloc static string struct switch this throw true try typeof " .
			"uint ulong unchecked unsafe ushort using virtual volatile void while async await " .
			// Contextual keywords.
			"ascending descending from get group into join let orderby partial select set value var ".
			"where yield";
	}

	protected function getContainedModes() {
	
		return array(
			new Mode(array(
				"className" => "comment",
				"begin" => "\/\/\/", 
				"end" => "$", 
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						"className" => "xmlDocTag",
						"begin" => "\/\/\/|<!--|-->"
					)),
					new Mode(array(
						"className" => "xmlDocTag",
						"begin" => "<\/?", 
						"end" => ">"
					))
				)
			)),
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#", 
				"end" => "$",
				"keywords" => 
					"if else elif endif define undef warning error line region endregion pragma checksum"
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "@\"", 
				"end" => "\"",
				"contains" => array(
					new Mode(array(
						"begin" => "\"\""
					))
				)
			)),
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			$this->C_NUMBER_MODE
		);
	}

}

?>
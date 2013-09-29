<?php
/* Copyright (c)
 * - 2006-2013, Jonas Follesø (jonas@follesoe.no), highlight.js
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
 * This file is a direct port of fsharp.js, the F# language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class FSharp extends Language {
	
	protected function getName() {
		return "fsharp";
	}
	
	protected function getKeyWords() {
		return 
			"abstract and as assert base begin class default delegate do done " .
			"downcast downto elif else end exception extern false finally for " .
			"fun function global if in inherit inline interface internal lazy let " .
			"match member module mutable namespace new null of open or " .
			"override private public rec return sig static struct then to " .
			"true try type upcast use val void when while with yield";
	}

	protected function getContainedModes() {
		
		$tmp1 = clone $this->APOS_STRING_MODE;
		$tmp1->illegal = null;
		$tmp2 = clone $this->QUOTE_STRING_MODE;
		$tmp2->illegal = null;
	
		return array(
			new Mode(array(
				"className" => "string",
				"begin" => "@\"", 
				"end" => "\"",
				"contains" => [new Mode(array("begin" => "\"\""))]
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"\"\"", 
				"end" => "\"\"\""
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "\/\/", 
				"end" => "$", 
				"returnBegin" => true
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "\(\*", 
				"end" => "\*\)"
			)),
			new Mode(array(
				"className" => "class",
				"beginWithKeyword" => true, 
				"end" => "\(|=|$",
				"keywords" => "type",
				"contains" => [
					new Mode(array(
						"className" => "title",
						"begin" => $this->UNDERSCORE_IDENT_RE
					))
				]
			)),
			new Mode(array(
				"className" => "annotation",
				"begin" => "\[<", 
				"end" => ">\]"
			)),
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$tmp1,
			$tmp2,
			$this->C_NUMBER_MODE
		);
	}

}

?>
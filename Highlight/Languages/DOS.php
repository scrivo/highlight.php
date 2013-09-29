<?php
/* Copyright (c)
 * - 2006-2013, Alexander Makarov (http://rmcreative.ru), highlight.js
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
 * This file is a direct port of dos.js, the DOS/command batch language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class DOS extends Language {
	
	protected function getName() {
		return "dos";
	}
	
	protected function isCaseInsensitive() {
		return true; 
	}
	
	protected function getKeyWords() {
		return array(
			"flow" =>
				"if else goto for in do call exit not exist errorlevel defined equ neq lss leq gtr geq",
			"keyword" => 
				"shift cd dir echo setlocal endlocal set pause copy",
			"stream" =>
				"prn nul lpt3 lpt2 lpt1 con com4 com3 com2 com1 aux",
			"winutils" => 
				"ping net ipconfig taskkill xcopy ren del"
		);
	}

	protected function getContainedModes() {
		
		return array(
			new Mode(array(
				"className" => "envvar", 
				"begin" => "%%[^ ]"
			)),
			new Mode(array(
				"className" => "envvar", 
				"begin" => "%[^ ]+?%"
			)),
			new Mode(array(
				"className" => "envvar", 
				"begin" => "![^ ]+?!"
			)),
			new Mode(array(
				"className" => "number", 
				"begin" => "\b\d+",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "@?rem", 
				"end" => "$"
			))
		);
		
	}

}

?>
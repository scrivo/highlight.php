<?php
/* Copyright (c)
 * - 2006-2013, Sergey Ignatov <sergey@ignatov.spb.su>, highlight.js
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
 * This file is a direct port of erlang-repl.js, the Erlang REPL language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class ErlangREPL extends Language {
	
	protected function getName() {
		return "erlang-repl";
	}
	
	protected function getKeyWords() {
		return array(
			"special_functions" =>
				"spawn spawn_link self",
			"reserved" =>
				"after and andalso|10 band begin bnot bor bsl bsr bxor case catch cond div end fun if " .
				"let not of or orelse|10 query receive rem try when xor"
		);
	}

	protected function getContainedModes() {
		
		return array(
			new Mode(array(
				"className" => "prompt", 
				"begin" => "^[0-9]+> ",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "%", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "number",
				"begin" => "\b(\d+#[a-fA-F0-9]+|\d+(\.\d+)?([eE][-+]?\d+)?)",
				"relevance" => 0
			)),
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "constant", 
				"begin" => "\?(::)?([A-Z]\w*(::)?)+"
			)),
			new Mode(array(
				"className" => "arrow", 
				"begin" => "->"
			)),
			new Mode(array(
				"className" => "ok", 
				"begin" => "ok"
			)),
			new Mode(array(
				"className" => "exclamation_mark", 
				"begin" => "!"
			)),
			new Mode(array(
				"className" => "function_or_atom",
				"begin" => "(\b[a-z\'][a-zA-Z0-9_\']*:[a-z\'][a-zA-Z0-9_\']*)|(\b[a-z\'][a-zA-Z0-9_\']*)",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "[A-Z][a-zA-Z0-9_\']*",
				"relevance" => 0
			))
		);
		
	}

}

?>
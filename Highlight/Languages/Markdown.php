<?php
/* Copyright (c)
 * - 2006-2013, John Crepezzi (john.crepezzi@gmail.com), highlight.js
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
 * This file is a direct port of markdown.js, the Markdown language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Markdown extends Language {

	protected function getName() {
		return "markdown";
	}
	
	protected function getKeyWords() {
		return null;
	}
	
	protected function getContainedModes() {

		return array(
			// highlight headers
			new Mode(array(
				"className" => "header",
				"begin" => "^#{1,3}", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "header",
				"begin" => "^.+?\n[=-]{2,}$"
			)),
			// inline html
			new Mode(array(
				"begin" => "<", 
				"end" => ">",
				"subLanguage" => "xml",
				"relevance" => 0
			)),
			// lists (indicators only)
			new Mode(array(
				"className" => "bullet",
				"begin" => "^([*+-]|(\d+\.))\s+"
			)),
			// strong segments
			new Mode(array(
				"className" => "strong",
				"begin" => "[*_]{2}.+?[*_]{2}"
			)),
			// emphasis segments
			new Mode(array(
				"className" => "emphasis",
				"begin" => "\*.+?\*"
			)),
			new Mode(array(
				"className" => "emphasis",
				"begin" => "_.+?_",
				"relevance" => 0
			)),
			// blockquotes
			new Mode(array(
				"className" => "blockquote",
				"begin" => "^>\s+", 
				"end" => "$"
			)),
			// code snippets
			new Mode(array(
				"className" => "code",
				"begin" => "`.+?`"
			)),
			new Mode(array(
				"className" => "code",
				"begin" => "^    ", 
				"end" => "$",
				"relevance" => 0
			)),
			// horizontal rules
			new Mode(array(
				"className" => "horizontal_rule",
				"begin" => "^-{3,}", 
				"end" => "$"
			)),
			// using links - title and link
			new Mode(array(
				"begin" => "\[.+?\]\(.+?\)",
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						"className" => "link_label",
						"begin" => "\[.+\]"
					)),
					new Mode(array(
						"className" => "link_url",
						"begin" => "\(", 
						"end" => "\)",
						"excludeBegin" => true, "excludeEnd" => true
					))
				)
			))
		);
	
	}
}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Dan Allen (dan.j.allen@gmail.com), highlight.js
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
 * This file is a direct port of asciidoc.js, the AsciiDoc language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class AsciiDoc extends Language {

	protected function getName() {
		return "asciidoc";
	}
	
	protected function getKeyWords() {
		return null;
	}
	
	protected function getContainedModes() {

		return array(
			// block comment
			new Mode(array(
				"className" => "comment",
				"begin" => "^\/{4,}\n",
				"end" => "\n\/{4,}$",
				// can also be done as...
				//"begin" => "^\/{4,))$",
				//"end" => "^\/{4,))$",
				"relevance" => 10
			)),
			// line comment
			new Mode(array(
				"className" => "comment",
				"begin" => "^\/\/",
				"end" => "$",
				"relevance" => 0
			)),
			// title
			new Mode(array(
				"className" => "title",
				"begin" => "^\.\w.*$"
			)),
			// example, admonition & sidebar blocks
			new Mode(array(
				"begin" => "^[=\*]{4,}\n",
				"end" => "\n^[=\*]{4,}$",
				"relevance" => 10
			)),
			// headings
			new Mode(array(
				"className" => "header",
				"begin" => "^(={1,5}) .+?( \1)?$",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "header",
				"begin" => "^[^\[\]\n]+?\n[=\-~\^\+]{2,}$",
				"relevance" => 10
			)),
			// document attributes
			new Mode(array(
				"className" => "attribute",
				"begin" => "^:.+?:",
				"end" => "\s",
				"excludeEnd" => true,
				"relevance" => 10
			)),
			// block attributes
			new Mode(array(
				"className" => "attribute",
				"begin" => "^\[.+?\]$",
				"relevance" => 0
			)),
			// quoteblocks
			new Mode(array(
				"className" => "blockquote",
				"begin" => "^_{4,}\n",
				"end" => "\n_{4,}$",
				"relevance" => 10
			)),
			// listing and literal blocks
			new Mode(array(
				"className" => "code",
				"begin" => "^[\-\.]{4,}\n",
				"end" => "\n[\-\.]{4,}$",
				"relevance" => 10
			)),
			// passthrough blocks
			new Mode(array(
				"begin" => "^\+{4,}\n",
				"end" => "\n\+{4,}$",
				"contains" => array(
					new Mode(array(
						"begin" => "<", 
						"end" => ">",
						"subLanguage" => "xml",
						"relevance" => 0
					))
				),
				"relevance" => 10
			)),
			// lists (can only capture indicators)
			new Mode(array(
				"className" => "bullet",
				"begin" => "^(\*+|\-+|\.+|[^\n]+?::)\s+"
			)),
			// admonition
			new Mode(array(
				"className" => "label",
				"begin" => "^(NOTE|TIP|IMPORTANT|WARNING|CAUTION):\s+",
				"relevance" => 10
			)),
			// inline strong
			new Mode(array(
				"className" => "strong",
				// must not follow a word character or be followed by an asterisk or space
				"begin" => "\B\*(?![\*\s])",
				"end" => "(\n{2}|\*)",
				// allow escaped asterisk followed by word char
				"contains" => array(
					new Mode(array(
						"begin" => "\\\\*\w",
						"relevance" => 0
					))
				)
			)),
			// inline emphasis
			new Mode(array(
				"className" => "emphasis",
				// must not follow a word character or be followed by a single quote or space
				"begin" => "\B'(?!['\s])",
				"end" => "(\n{2}|')",
				// allow escaped single quote followed by word char
				"contains" => array(
					new Mode(array(
						"begin" => "\\\\'\w",
						"relevance" => 0
					))
				),
				"relevance" => 0
			)),
			// inline emphasis (alt)
			new Mode(array(
				"className" => "emphasis",
				// must not follow a word character or be followed by an underline or space
				"begin" => "_(?![_\s])",
				"end" => "(\n{2}|_)",
				"relevance" => 0
			)),
			// inline code snippets (TODO should get same treatment as strong and emphasis)
			new Mode(array(
				"className" => "code",
				"begin" => "(`.+?`|\+.+?\+)",
				"relevance" => 0
			)),
			// indented literal block
			new Mode(array(
				"className" => "code",
				"begin" => "^[ \t]",
				"end" => "$",
				"relevance" => 0
			)),
			// horizontal rules
			new Mode(array(
				"className" => "horizontal_rule",
				"begin" => "^'{4,}[ \t]*$",
				"relevance" => 10
			)),
			// images and links
			new Mode(array(
				"begin" => "(link:)?(http|https|ftp|file|irc|image:?):\S+\[.*?\]",
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						//"className" => "macro",
						"begin" => "(link|image:?):",
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "link_url",
						"begin" => "\w",
						"end" => "[^\[]+",
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "link_label",
						"begin" => "\[",
						"end" => "\]",
						"excludeBegin" => true,
						"excludeEnd" => true,
						"relevance" => 0
					))
				),
				"relevance" => 10
			))
		);
		
	}
}

?>
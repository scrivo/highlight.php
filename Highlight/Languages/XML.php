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
 * This file is a direct port of xml.js, the XML/HTML language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class XML extends Language {

	protected function getName() {
		return "xml";
	}
	
	protected function getKeyWords() {
		return null;
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getContainedModes() {

		$XML_IDENT_RE = "[A-Za-z0-9\\._:-]+";

		$TAG_INTERNALS = new Mode(array(
			"endsWithParent" => true,
			"relevance" => 0,
			"contains" => array(
				new Mode(array(
					"className" => "attribute",
					"begin" => $XML_IDENT_RE,
					"relevance" => 0
				)),
				new Mode(array(
					"begin" => "=\s*\"", 
					"returnBegin" => true, 
					"end" => "\"",
					"contains" => array(
						new Mode(array(
							"className" => "value",
							"begin" => "\"", 
							"endsWithParent" => true
						))
					)
				)),
				new Mode(array(
					"begin" => "=\s*'", 
					"returnBegin" => true, 
					"end" => "'",
					"contains" => array(
						new Mode(array(
							"className" => "value",
							"begin" => "'", 
							"endsWithParent" => true
						))
					)
				)),
				new Mode(array(
					"begin" => "=",
					"contains" => array(
						new Mode(array(
							"className" => "value",
							"begin" => "[^\s>]"
						))
					)
				))
			)
		));
		
		return array(
			new Mode(array(
				"className" => "pi",
				"begin" => "<\?", 
				"end" => "\?>",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "doctype",
				"begin" => "<!DOCTYPE", 
				"end" => ">",
				"relevance" => 10,
				"contains" => array(
					new Mode(array(
						"begin" => "\[", 
						"end" => "\]"
					))
				)
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "<!--", 
				"end" => "-->",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "cdata",
				"begin" => "<!\[CDATA\[", 
				"end" => "\]\]>",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "tag",
				/*
				 The lookahead pattern (?=...) ensures that 'begin' only matches
				 '<style' as a single word, followed by a whitespace or an
				 ending braket. The '$' is needed for the lexem to be recognized
				 by hljs.subMode() that tests lexems outside the stream.
				 */
				"begin" => "<style(?=\s|>|$)", 
				"end" => ">",
				"keywords" => array(
					"title" => "style"
				),
				"contains" => array(
					$TAG_INTERNALS
				),
				"starts" => new Mode(array(
					"end" => "<\/style>", 
					"returnEnd" => true,
					"subLanguage" => "css"
				))
			)),
			new Mode(array(
				"className" => "tag",
				// See the comment in the <style tag about the lookahead pattern
				"begin" => "<script(?=\s|>|$)", 
				"end" => ">",
				"keywords" => array(
					"title" => "script"
				),
				"contains" => array(
					$TAG_INTERNALS
				),
				"starts" => new Mode(array(
					"end" => "<\/script>", 
					"returnEnd" => true,
					"subLanguage" => "javascript"
				))
			)),
			new Mode(array(
				"begin" => "<%", 
				"end" => "%>",
				"subLanguage" => "vbscript"
			)),
			new Mode(array(
				"className" => "tag",
				"begin" => "<\/?", 
				"end" => "\/?>",
				"relevance" => 0,
				"contains" => array(
					new Mode(array(
						"className" => "title", 
						"begin" => "[^ \/><]+"
					)),
					$TAG_INTERNALS
				)
			))
		);
	
	}
}

?>
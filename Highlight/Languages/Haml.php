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
 * This file is a direct port of haml.js, the Haml language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Haml extends Language {

	protected function getName() {
		return "haml";
	}
	
	protected function getKeyWords() {
		return null;
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getContainedModes() {

		return array(
			new Mode(array(
				"className" => "doctype",
				"begin" => "^!!!( (5|1\.1|Strict|Frameset|Basic|Mobile|RDFa|XML\b.*))?$",
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "comment",
				// FIXME these comments should be allowed to span indented lines
				"begin" => "^\s*(-#|\/).*$",
				"relevance" => 0
			)),
			new Mode(array(
				"begin" => "^\s*-(?!#)",
				"starts" => new Mode(array(
					"end" => "\n",
					"subLanguage" => "ruby"
				)),
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "tag",
				"begin" => "^\s*%",
				"contains" => array(
					new Mode(array(
						"className" => "title",
						"begin" => "\w+",
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "value",
						"begin" => "[#\.]\w+",
						"relevance" => 0
					)),
					new Mode(array(
						"begin" => "{\s*",
						"end" => "\s*}",
						"excludeEnd" => true,
						"contains" => array(
							new Mode(array(
								//"className" => 'attribute',
								"begin" => ":\w+\s*=>",
								"end" => ",\s+",
								"returnBegin" => true,
								"endsWithParent" => true,
								"relevance" => 0,
								"contains" => array(
									new Mode(array(
										"className" => "symbol",
										"begin" => ":\w+",
										"relevance" => 0
									)),
									new Mode(array(
										"className" => "string",
										"begin" => "\"",
										"end" => "\"",
										"relevance" => 0
									)),
									new Mode(array(
										"className" => "string",
										"begin" => "'",
										"end" => "'",
										"relevance" => 0
									)),
									new Mode(array(
										"begin" => "\w+",
										"relevance" => 0
									))
								)
							)),
						),
						"relevance" => 0
					)),
					new Mode(array(
						"begin" => "\(\s*",
						"end" => "\s*\)",
						"excludeEnd" => true,
						"contains" => array(
							new Mode(array(
								//"className" => 'attribute',
								"begin" => "\w+\s*=",
								"end" => "\s+",
								"returnBegin" => true,
								"endsWithParent" => true,
								"relevance" => 0,
								"contains" => array(
									new Mode(array(
										"className" => "attribute",
										"begin" => "\w+",
										"relevance" => 0
									)),
									new Mode(array(
										"className" => "string",
										"begin" => "\"",
										"end" => "\"",
										"relevance" => 0
									)),
									new Mode(array(
										"className" => "string",
										"begin" => "'",
										"end" => "'",
										"relevance" => 0
									)),
									new Mode(array(
										"begin" => "\w+",
										"relevance" => 0
									))
								)
							)),
						),
						"relevance" => 0
					))
				),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "bullet",
				"begin" => "^\s*[=~]\s*",
				"relevance" => 0
			)),
			new Mode(array(
				"begin" => "#{",
				"starts" => new Mode(array(
					"end" => "}",
					"subLanguage" => "ruby"
				)),
				"relevance" => 0
			))	
		);
	}
}

?>
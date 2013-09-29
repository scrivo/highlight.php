<?php
/* Copyright (c)
 * - 2006-2013, Anton Kovalyov (anton@kovalyov.net), highlight.js 
 *              (original author)
 * - 2006-2013, Peter Leonov (gojpeg@yandex.ru), highlight.js 
 * - 2006-2013, Vasily Polovnyov (vast@whiteants.net), highlight.js 
 * - 2006-2013, Loren Segal (lsegal@soen.ca), highlight.js 
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
 * This file is a direct port of ruby.js, the Ruby language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Ruby extends Language {
	
	protected function getName() {
		return "ruby";
	}
	
	private function GET_KEYWORDS() {
		return 
			"and false then defined module in return redo if BEGIN retry end for true self when " .
			"next until do begin unless END rescue nil else break undef not super class case " .
			"require yield alias while ensure elsif or include";
	}

	protected function getKeywords() {
		return $this->GET_KEYWORDS();
	}
	
	protected function getLexems() {
		return "[a-zA-Z_][a-zA-Z0-9_]*(\!|\?)?";
	}
	
	protected function getContainedModes() {
		
		$RUBY_IDENT_RE = "[a-zA-Z_][a-zA-Z0-9_]*(\!|\?)?";
		$RUBY_METHOD_RE = "[a-zA-Z_]\w*[!?=]?|[-+~]\@|<<|>>|=~|===?|<=>|[<>]=?|\*\*|[-\/+%^&*~`|]|\[\]=?";

		$YARDOCTAG = new Mode(array(
			"className" => "yardoctag",
			"begin" => "@[A-Za-z]+"
		));

		$COMMENTS = array(
			new Mode(array(
				"className" => "comment",
				"begin" => "#",
				"end" => "$",
				"contains" => array($YARDOCTAG)
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "^\=begin",
				"end" => "^\=end",
				"contains" => array($YARDOCTAG),
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "^__END__",
				"end" => "\n$"
			))
		);

		$SUBST = new Mode(array(
			"className" => "subst",
			"begin" => "#{",
			"end" => "}",
			"lexems" => $RUBY_IDENT_RE,
			"keywords" => $this->GET_KEYWORDS()
		));

		$STR_CONTAINS = array($this->BACKSLASH_ESCAPE, $SUBST);

		$STRINGS = array(
			new Mode(array(
				"className" => "string",
				"begin" => "'",
				"end" => "'",
				"contains" => $STR_CONTAINS,
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"",
				"end" => "\"",
				"contains" => $STR_CONTAINS,
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?\(",
				"end" => "\)",
				"contains" => $STR_CONTAINS
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?\[",
				"end" => "\]",
				"contains" => $STR_CONTAINS
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?{",
				"end" => "}",
				"contains" => $STR_CONTAINS
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?<",
				"end" => ">",
				"contains" => $STR_CONTAINS,
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?\/",
				"end" => "\/",
				"contains" => $STR_CONTAINS,
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?%",
				"end" => "%",
				"contains" => $STR_CONTAINS,
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?-",
				"end" => "-",
				"contains" => $STR_CONTAINS,
				"relevance" => 10
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "%[qw]?\|",
				"end" => "\|",
				"contains" => $STR_CONTAINS,
				"relevance" => 10
			))
		);
		
		$FUNCTION =	new Mode(array(
			"className" => "function",
			"beginWithKeyword" => true,
			"end" => " |$|;",
			"keywords" => "def",
			"contains" => array_merge(
				array(
					new Mode(array(
						"className" => "title",
						"begin" => $RUBY_METHOD_RE,
						"lexems" => $RUBY_IDENT_RE,
						"keywords" => $this->GET_KEYWORDS()
					)),
					new Mode(array(
						"className" => "params",
						"begin" => "\(",
						"end" => "\)",
						"lexems" => $RUBY_IDENT_RE,
						"keywords" => $this->GET_KEYWORDS()
					))
				),
				$COMMENTS
			)
		));
		
		$RUBY_DEFAULT_CONTAINS = array_merge(
			$COMMENTS,
			$STRINGS,
			array(
				new Mode(array(
					"className" => "class",
					"beginWithKeyword" => true,
					"end" => "$|;",
					"keywords" => "class module",
					"contains" => array_merge( 
						array(
							new Mode(array(
								"className" => "title",
								"begin" => "[A-Za-z_]\w*(::\w+)*(\?|\!)?",
								"relevance" => 0
							)),
							new Mode(array(
								"className" => "inheritance",
								"begin" => "<\s*",
								"contains" => array(
									new Mode(array(
										"className" => "parent",
										"begin" => "(" . $this->IDENT_RE . "::)?" . $this->IDENT_RE
									))
								)
							))
						),
						$COMMENTS
					),
				)),
				$FUNCTION,
				new Mode(array(
					"className" => "constant",
					"begin" => "(::)?(\b[A-Z]\w*(::)?)+",
					"relevance" => 0
				)),
				new Mode(array(
					"className" => "symbol",
					"begin" => ":",
					"contains" => array_merge(
						$STRINGS,
						array(
							new Mode(array(
								"begin" => $RUBY_METHOD_RE
							))
						)
					),
					"relevance" => 0
				)),
				new Mode(array(
					"className" => "symbol",
					"begin" => $RUBY_IDENT_RE . ":",
					"relevance" => 0
				)),
				new Mode(array(
					"className" => "number",
					"begin" => "(\b0[0-7_]+)|(\b0x[0-9a-fA-F_]+)|(\b[1-9][0-9_]*(\.[0-9_]+)?)|[0_]\b",
					"relevance" => 0
				)),
				new Mode(array(
					"className" => "number",
					"begin" => "\?\w"
				)),
				new Mode(array(
					"className" => "variable",
					"begin" => "(\\$\W)|((\\$|\@\@?)(\w+))"
				)),
				new Mode(array( // regexp container
					"begin" => "(" . $this->RE_STARTERS_RE . ")\s*",
					"contains" => array_merge(
						$COMMENTS,
						array(
							new Mode(array(
								"className" => "regexp",
								"begin" => "\/",
								"end" => "\/[a-z]*",
								"illegal" => "\n",
								"contains" => array($this->BACKSLASH_ESCAPE, $SUBST)
							))
						)
					),
					"relevance" => 0
				)),
			)
		);
				
		$SUBST->contains = $RUBY_DEFAULT_CONTAINS;
		$FUNCTION->contains[1]->contains = $RUBY_DEFAULT_CONTAINS;
		
		return $RUBY_DEFAULT_CONTAINS;
		
	}

}

?>
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
 * This file is a direct port of css.js, the CSS language definition file 
 * for highlight.js, to PHP.
 * @see "https" =>//github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class CSS extends Language {
	
	protected function getName() {
		return "css";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return null;
	}

	protected function getIllegal() {
		return "[=\/|']";
	}
	
	protected function getContainedModes() {
	
		$IDENT_RE = "[a-zA-Z-][a-zA-Z0-9_-]*";
		
		$FUNCTION = new Mode(array(
			"className" => "function",
			"begin" => $IDENT_RE . "\(", 
			"end" => "\)",
			"contains" => array(
				"self", 
				$this->NUMBER_MODE, 
				$this->APOS_STRING_MODE, 
				$this->QUOTE_STRING_MODE
			)
		));
			
		return array(
			$this->C_BLOCK_COMMENT_MODE,
			new Mode(array(
				"className" => "id", 
				"begin" => "#[A-Za-z0-9_-]+"
			)),
			new Mode(array(
				"className" => "class", 
				"begin" => "\.[A-Za-z0-9_-]+",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "attr_selector",
				"begin" => "\[", 
				"end" => "\]",
				"illegal" => "$"
			)),
			new Mode(array(
				"className" => "pseudo",
				"begin" => ":(:)?[a-zA-Z0-9_\-+()\"']+"
			)),
			new Mode(array(
				"className" => "at_rule",
				"begin" => "@(font-face|page)",
				"lexems" => "[a-z-]+",
				"keywords" => explode(" ", "font-face page")
			)),
			new Mode(array(
				"className" => "at_rule",
				"begin" => "@", 
				"end" => "[{;]", // at_rule eating first "{" is a good thing
								 // because it doesn’t let it to be parsed as
								 // a rule set but instead drops parser into
								 // the default mode which is how it should be.
				"contains" => array(
					new Mode(array(
						"className" => "keyword",
						"begin" => "\S+"
					)),
					new Mode(array(
						"begin" => "\s", 
						"endsWithParent" => true, 
						"excludeEnd" => true,
						"relevance" => 0,
						"contains" => array(
							$FUNCTION,
							$this->APOS_STRING_MODE, 
							$this->QUOTE_STRING_MODE,
							$this->NUMBER_MODE
						)
					))
				)
			)),
			new Mode(array(
				"className" => "tag", 
				"begin" => $IDENT_RE,
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "rules",
				"begin" => "{", 
				"end" => "}",
				"illegal" => "[^\s]",
				"relevance" => 0,
				"contains" => array(
					$this->C_BLOCK_COMMENT_MODE,
					new Mode(array(
						"className" => "rule",
						"begin" => "[^\s]", 
						"returnBegin" => true, 
						"end" => ";", 
						"endsWithParent" => true,
						"contains" => array(
							new Mode(array(
								"className" => "attribute",
								"begin" => "[A-Z_.\-]+", 
								"end" => ":",
								"excludeEnd" => true,
								"illegal" => "[^\\s]",
								"starts" => new Mode(array(
									"className" => "value",
									"endsWithParent" => true, 
									"excludeEnd" => true,
									"contains" => array(
										$FUNCTION,
										$this->NUMBER_MODE,
										$this->QUOTE_STRING_MODE,
										$this->APOS_STRING_MODE,
										$this->C_BLOCK_COMMENT_MODE,
										new Mode(array(
											"className" => "hexcolor", 
											"begin" => "#[0-9A-Fa-f]+"
										)),
										new Mode(array(
											"className" => "important", 
											"begin" => "!important"
										))
									)
								))
							))
						)
					))
				)
			))
		);
	}
}

?>
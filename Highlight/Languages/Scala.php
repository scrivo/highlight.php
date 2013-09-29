<?php
/* Copyright (c)
 * - 2006-2013, Jan Berkel (jan.berkel@gmail.com), highlight.js
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
 * This file is a direct port of scala.js, the Scala language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Scala extends Language {
	
	protected function getName() {
		return "scala";
	}
	
	protected function getKeyWords() {
		return 
			"type yield lazy override def with val var false true sealed abstract private trait " .
			"object null if for while throw finally protected extends import final return else " .
			"break new catch super class case package default try this match continue throws";
	}

	protected function getContainedModes() {
		
		$ANNOTATION = new Mode(array(
			"className" => "annotation", 
			"begin" => "@[A-Za-z]+"
		));
		
		$STRING = new Mode(array(
			"className" => "string",
			"begin" => "u?r?\"\"\"", 
			"end" => "\"\"\"",
			"relevance" => 10
		));

		return array(
			new Mode(array(
				"className" => "javadoc",
				"begin" => "\/\*\*", 
				"end" => "\*\/",
				"contains" => array(
					new Mode(array(
						"className" => "javadoctag",
						"begin" => "@[A-Za-z]+"
					))
				),
				"relevance" => 10
			)),
			$this->C_LINE_COMMENT_MODE, 
			$this->C_BLOCK_COMMENT_MODE,
			$this->APOS_STRING_MODE, 
			$this->QUOTE_STRING_MODE, 
			$STRING,
			new Mode(array(
				"className" => "class",
				"begin" => "((case )?class |object |trait )", 
				"end" => "({|$)", // beginWithKeyword won't work because a single "case" shouldn't start this mode
				"illegal" => ":",
				"keywords" => "case class trait object",
				"contains" => array(
					new Mode(array(
						"beginWithKeyword" => true,
						"keywords" => "extends with",
						"relevance" => 10
					)),
					new Mode(array(
						"className" => "title",
						"begin" => $this->UNDERSCORE_IDENT_RE
					)),
					new Mode(array(
						"className" => "params",
						"begin" => "\(", 
						"end" => "\)",
						"contains" => array(
							$this->APOS_STRING_MODE, 
							$this->QUOTE_STRING_MODE, 
							$STRING,
							$ANNOTATION
						)
					))
				)
			)),
			$this->C_NUMBER_MODE,
			$ANNOTATION
		);
	}

}

?>
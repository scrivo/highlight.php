<?php
/* Copyright (c)
 * - 2006-2013, Jeremy Hull (sourdrums@gmail.com), highlight.js
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
 * This file is a direct port of haskell.js, the Haskell language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Haskell extends Language {
	
	protected function getName() {
		return "haskell";
	}
	
	protected function getKeyWords() {
		return 
			"let in if then else case of where do module import hiding qualified type data " .
			"newtype deriving class instance not as foreign ccall safe unsafe";
	}

	protected function getContainedModes() {
		
		$TYPE = new Mode(array(
			"className" => "type",
			"begin" => "\b[A-Z][\w']*",
			"relevance" => 0
		));
		
		$CONTAINER = new Mode(array(
			"className" => "container",
			"begin" => "\(", 
			"end" => "\)",
			"illegal" => "\"",
			"contains" => array(
				new Mode(array(
					"className" => "type", 
					"begin" => "\b[A-Z][\w]*(\((\.\.|,|\w+)\))?"
				)),
				new Mode(array(
					"className" => "title", 
					"begin" => "[_a-z][\w']*"
				))
			)
		));
		
		$CONTAINER2 = new Mode(array(
			"className" => "container",
			"begin" => "{", 
			"end" => "}",
			"contains" => $CONTAINER->contains
		));
	
		return array(
			new Mode(array(
				"className" => "comment",
				"begin" => "--", 
				"end" => "$"
			)),
			new Mode(array(
			"className" => "preprocessor",
				"begin" => "{-#", 
				"end" => "#-}"
			)),
			new Mode(array(
				"className" => "comment",
				"contains" => array("self"),
				"begin" => "{-", 
				"end" => "-}"
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\s+'", 
				"end" => "'",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 0
			)),
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "import",
				"begin" => "\bimport", 
				"end" => "$",
				"keywords" => "import qualified as hiding",
				"contains" => array($CONTAINER),
				"illegal" => "\W\.|;"
			)),
			new Mode(array(
				"className" => "module",
				"begin" => "\bmodule", 
				"end" => "where",
				"keywords" => "module where",
				"contains" => array($CONTAINER),
				"illegal" => "\W\.|;"
			)),
			new Mode(array(
				"className" => "class",
				"begin" => "\b(class|instance)", 
				"end" => "where",
				"keywords" => "class where instance",
				"contains" => array($TYPE)
			)),
			new Mode(array(
				"className" => "typedef",
				"begin" => "\b(data|(new)?type)", 
				"end" => "$",
				"keywords" => "data type newtype deriving",
				"contains" => array($TYPE, $CONTAINER, $CONTAINER2)
			)),
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "shebang",
				"begin" => "#!\/usr\/bin\/env\ runhaskell", 
				"end" => "$"
			)),
			$TYPE,
			new Mode(array(
				"className" => "title", 
				"begin" => "^[_a-z][\w']*"
			)),
			new Mode(array(
				"begin" => "->|<-"
			)) // No markup, relevance booster
		);
	}

}

?>
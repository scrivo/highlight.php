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
 * This file is a direct port of json.js, the JSON language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class JSON extends Language {
	
	protected function getName() {
		return "json";
	}
	
	protected function getIllegal() {
		return "\S";
	}
	
	protected function getKeyWords() {
		return array(
			"literal" =>
				"true false null"
		);
	}

	protected function getContainedModes() {
		
		$TYPES = array(
			$this->QUOTE_STRING_MODE,
			$this->C_NUMBER_MODE
		);
		
		$VALUE_CONTAINER = new Mode(array(
			"className"=> "value",
			"end" => ",", 
			"endsWithParent" => true, 
			"excludeEnd" => true,
			"keywords" => $this->getKeyWords()
		));
		$VALUE_CONTAINER->contains = &$TYPES;
		
		$OBJECT = new Mode(array(
			"begin" => "{", 
			"end" => "}",
			"contains" => array(
				new Mode(array(
					"className" => "attribute",
					"begin" => "\s*\"", 
					"end" => "\"\s*:\s*", 
					"excludeBegin" => true, 
					"excludeEnd" => true,
					"contains" => array(
						$this->BACKSLASH_ESCAPE
					),
					"illegal" => "\n",
					"starts" => $VALUE_CONTAINER
				))
			),
			"illegal" => "\S"
		));
		
		$tmp = clone $VALUE_CONTAINER;
		$tmp->className = null;
		
		$ARRAY= new Mode(array(
			"className" => "xxx", 
			"begin" => "\[", 
			"end" => "\]",
			"contains" => array($tmp), // inherit is also a workaround for a 
				// bug that makes shared modes with endsWithParent compile 
				// only the ending of one of the parents
			"illegal" => "\S"
		));
		
		$TYPES[] = $OBJECT;
		$TYPES[] = $ARRAY;
		return $TYPES;
	}

}

?>
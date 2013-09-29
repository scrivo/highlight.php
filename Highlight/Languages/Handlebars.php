<?php
/* Copyright (c)
 * - 2006-2013, Robin Ward (robin.ward@gmail.com), highlight.js
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
 * This file is a direct port of handlebars.js, the Handlebars language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Handlebars extends XML {

	private $EXPRESSION_KEYWORDS = 
		"each in with if else unless bindattr action collection debugger log outlet template unbound view yield";
	
	protected function getName() {
		return "handlebars";
	}
	
	private function getVariableContains() {
		return new Mode(array(
			"className" => "variable", 
			"begin" => "[a-zA-Z\.]+",
			"keywords" => $this->EXPRESSION_KEYWORDS
		));
	}	
	
	private function getHandlebarsContains() {
		return array(
			new Mode(array(
				"className" => "expression",
				"begin" => "{{", 
				"end" => "}}",
				"contains" => array(
					new Mode(array(
						"className" => "begin-block", 
						"begin" => "\#[a-zA-Z\ \.]+",
						"keywords" => $this->EXPRESSION_KEYWORDS
					)),
					new Mode(array(
						"className" => "string",
						"begin" => "\"", 
						"end" => "\""
					)),
					new Mode(array(
						"className" => "end-block", 
						"begin" => "\\/[a-zA-Z\ \.]+",
						"keywords" => $this->EXPRESSION_KEYWORDS
					)),
					new Mode(array(
						"className" => "variable", 
						"begin" => "[a-zA-Z\.]+",
						"keywords" => $this->EXPRESSION_KEYWORDS
					))
				)
			))
		);
	}
	
	private function copy($mode, $parent=null) {
		$contains = array();
		for ($i=0; isset($mode->contains) && $i<count($mode->contains); $i++) {
			$contains[] = $this->copy($mode->contains[$i], $mode);
		}
		$contains = array_merge($this->getHandlebarsContains(), $contains);
		if (isset($mode->contains) && count($contains)) {
			$mode->contains = $contains;
		}
		return $mode;
	}

	protected function getContainedModes() {
		$this->contains = parent::getContainedModes();
		$this->copy($this);
		return $this->contains;
	}
}

?>
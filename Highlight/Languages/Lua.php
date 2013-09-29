<?php
/* Copyright (c)
 * - 2006-2013, Andrew Fedorov (dmmdrs@mail.ru), highlight.js (original author)
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
 * This file is a direct port of lue.js, the Lua language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Lua extends Language {
	
	protected function getName() {
		return "lua";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"and break do else elseif end false for if in local nil not or repeat return then " .
				"true until while",
			"built_in" =>
				"_G _VERSION assert collectgarbage dofile error getfenv getmetatable ipairs load " .
				"loadfile loadstring module next pairs pcall print rawequal rawget rawset require " .
				"select setfenv setmetatable tonumber tostring type unpack xpcall coroutine debug " .
				"io math os package string table"
		);
	}
	
	protected function getLexems() {
		return $this->UNDERSCORE_IDENT_RE;
	}
	
	protected function getContainedModes() {
		
		$OPENING_LONG_BRACKET = "\[=*\[";
		$CLOSING_LONG_BRACKET = "\]=*\]";
		
		$LONG_BRACKETS = new Mode(array(
			"begin" => $OPENING_LONG_BRACKET, 
			"end" => $CLOSING_LONG_BRACKET,
			"contains" => array("self")
		));

		$COMMENTS = array(
			new Mode(array(
				"className" => "comment",
				"begin" => "--(?!" . $OPENING_LONG_BRACKET . ")", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "comment",
				"begin" => "--" . $OPENING_LONG_BRACKET, 
				"end" => $CLOSING_LONG_BRACKET,
				"contains" => [$LONG_BRACKETS],
				"relevance" => 10
			))
		);
		
		return array_merge(
			$COMMENTS,
			array(
				new Mode(array(
					"className" => "function",
					"beginWithKeyword" => true, 
					"end" => "\)",
					"keywords" => "function",
					"contains" => array_merge(
						array(
							new Mode(array(
								"className" => "title",
								"begin" => "([_a-zA-Z]\w*\.)*([_a-zA-Z]\w*:)?[_a-zA-Z]\w*"
							)),
							new Mode(array(
								"className" => "params",
								"begin" => "\(", 
								"endsWithParent" => true,
								"contains" => $COMMENTS
							))
						),
						$COMMENTS
					)
				)),
				$this->C_NUMBER_MODE,
				$this->APOS_STRING_MODE,
				$this->QUOTE_STRING_MODE,
				new Mode(array(
					"className" => "string",
					"begin" => $OPENING_LONG_BRACKET, 
					"end" => $CLOSING_LONG_BRACKET,
					"contains" => array($LONG_BRACKETS),
					"relevance" => 10
				))
			)
		);
		
	}

}

?>
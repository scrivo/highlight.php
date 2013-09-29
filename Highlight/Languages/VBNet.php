<?php
/* Copyright (c)
 * - 2006-2013, Poren Chiang (ren.chiang@gmail.com), highlight.js
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
 * This file is a direct port of vbnet.js, the VB.Net language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class VBNet extends Language {
	
	protected function getName() {
		return "vbnet";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getIllegal() {
		return "(\/\/|endif|gosub|variant|wend)"; /* reserved deprecated keywords */
	}

	protected function getKeyWords() {
		return array(
			"keyword" =>
				"addhandler addressof alias and andalso aggregate ansi as assembly auto binary by byref byval " . /* a-b */
				"call case catch class compare const continue custom declare default delegate dim distinct do " . /* c-d */
				"each equals else elseif end enum erase error event exit explicit finally for friend from function " . /* e-f */
				"get global goto group handles if implements imports in inherits interface into is isfalse isnot istrue " . /* g-i */
				"join key let lib like loop me mid mod module mustinherit mustoverride mybase myclass " . /* j-m */
				"namespace narrowing new next not notinheritable notoverridable " . /* n */
				"of off on operator option optional or order orelse overloads overridable overrides " . /* o */
				"paramarray partial preserve private property protected public " . /* p */
				"raiseevent readonly redim rem removehandler resume return " . /* r */
				"select set shadows shared skip static step stop structure strict sub synclock " . /* s */
				"take text then throw to try unicode until using when where while widening with withevents writeonly xor", /* t-x */
			"built_in" =>
				"boolean byte cbool cbyte cchar cdate cdec cdbl char cint clng cobj csbyte cshort csng cstr ctype " .  /* b-c */
				"date decimal directcast double gettype getxmlnamespace iif integer long object " . /* d-o */
				"sbyte short single string trycast typeof uinteger ulong ushort", /* s-u */
			"literal" =>
				"true false nothing"
		);
	}
	
	protected function getContainedModes() {
		
		$TMP = clone $this->QUOTE_STRING_MODE;
		$TMP->contains = array(
			new Mode(array(
				"begin" => "\"\""
			))
		);
		
		return array(
			$TMP,
			new Mode(array(
				"className" => "comment",
				"begin" => "'", 
				"end" => "$", 
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						"className" => "xmlDocTag",
						"begin" => "'''|<!--|-->"
					)),
					new Mode(array(
						"className" => "xmlDocTag",
						"begin" => "<\/?", 
						"end" => ">"
					)),
				)
			)),
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#", 
				"end" => "$",
				"keywords" => "if else elseif end region externalsource"
			))	
		);
		
	}

}

?>
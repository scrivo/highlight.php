<?php
/* Copyright (c)
 * - 2006-2013, Nikita Ledyaev (lenikita@yandex.ru), highlight.js
 *              (original author)
 * - 2006-2013, Michal Gabrukiewicz (mgabru@gmail.com), highlight.js
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
 * This file is a direct port of vbscript.js, the VBScript language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class VBScript extends Language {
	
	protected function getName() {
		return "vbscript";
	}

	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getIllegal() {
		return "\/\/";
	}

	protected function getKeyWords() {
		return array(
			"keyword" =>
				"call class const dim do loop erase execute executeglobal exit for each next function " .
				"if then else on error option explicit new private property let get public randomize " .
				"redim rem select case set stop sub while wend with end to elseif is or xor and not " .
				"class_initialize class_terminate default preserve in me byval byref step resume goto",
			"built_in" =>
				"lcase month vartype instrrev ubound setlocale getobject rgb getref string " .
				"weekdayname rnd dateadd monthname now day minute isarray cbool round formatcurrency " .
				"conversions csng timevalue second year space abs clng timeserial fixs len asc " .
				"isempty maths dateserial atn timer isobject filter weekday datevalue ccur isdate " .
				"instr datediff formatdatetime replace isnull right sgn array snumeric log cdbl hex " .
				"chr lbound msgbox ucase getlocale cos cdate cbyte rtrim join hour oct typename trim " .
				"strcomp int createobject loadpicture tan formatnumber mid scriptenginebuildversion " .
				"scriptengine split scriptengineminorversion cint sin datepart ltrim sqr " .
				"scriptenginemajorversion time derived eval date formatpercent exp inputbox left ascw " .
				"chrw regexp server response request cstr err",
			"literal" =>
				"true false null nothing empty"
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
				"end" => "$"
			)),
			$this->C_NUMBER_MODE
		);
		
	}

}

?>
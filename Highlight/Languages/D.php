<?php
/* Copyright (c)
 * - 2006-2013, Aleksandar Ruzicic (aleksandar@ruzicic.info), highlight.js
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
 * This file is a direct port of d.js, the D language definition file 
 * for highlight.js, to PHP.
 * @see "https" =>//github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class D extends Language {
	
	protected function getName() {
		return "d";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"abstract alias align asm assert auto body break byte case cast catch class " .
				"const continue debug default delete deprecated do else enum export extern final " .
				"finally for foreach foreach_reverse|10 goto if immutable import in inout int " .
				"interface invariant is lazy macro mixin module new nothrow out override package " .
				"pragma private protected public pure ref return scope shared static struct " .
				"super switch synchronized template this throw try typedef typeid typeof union " .
				"unittest version void volatile while with __FILE__ __LINE__ __gshared|10 " .
				"__thread __traits __DATE__ __EOF__ __TIME__ __TIMESTAMP__ __VENDOR__ __VERSION__",
			"built_in" =>
				"bool cdouble cent cfloat char creal dchar delegate double dstring float function " .
				"idouble ifloat ireal long real short string ubyte ucent uint ulong ushort wchar " .
				"wstring",
			"literal" =>
				"false null true"
		);
	}

	protected function getLexems() {
		return $this->UNDERSCORE_IDENT_RE;
	}
	
	protected function getContainedModes() {
	
		/**
		 * Number literal regexps
		 *
		 * @type {String}
		 */
		$decimal_integer_re = "(0|[1-9][\d_]*)";
		$decimal_integer_nosus_re = "(0|[1-9][\d_]*|\d[\d_]*|[\d_]+?\d)";
		$binary_integer_re = "0[bB][01_]+";
		$hexadecimal_digits_re = "([\da-fA-F][\da-fA-F_]*|_[\da-fA-F][\da-fA-F_]*)";
		$hexadecimal_integer_re = "0[xX]" . $hexadecimal_digits_re;

		$decimal_exponent_re = "([eE][+-]?" . $decimal_integer_nosus_re . ")";
		$decimal_float_re = 
			"(" . $decimal_integer_nosus_re . "(\.\d*|" . $decimal_exponent_re . ")|" .
			"\d+\." . $decimal_integer_nosus_re . $decimal_integer_nosus_re . "|" .
			"\." . $decimal_integer_re . $decimal_exponent_re . "?" . ")";
		$hexadecimal_float_re = "(0[xX](" .
			$hexadecimal_digits_re . "\." . $hexadecimal_digits_re . "|".
			"\.?" . $hexadecimal_digits_re . ")[pP][+-]?" . $decimal_integer_nosus_re . ")";

		$integer_re = "(" .
			$decimal_integer_re . "|" .
			$binary_integer_re  . "|" .
		 	$hexadecimal_integer_re   .
		")";

		$float_re = "(" .
			$hexadecimal_float_re . "|" .
			$decimal_float_re  .
		")";
	
		/**
		 * Escape sequence supported in D string and character literals
		 *
		 * @type {String}
		 */
		$escape_sequence_re = "\\\\(" .
			"['\"\?\abfnrtv]|" . // common escapes
			"u[\dA-Fa-f]{4}|" .  // four hex digit unicode codepoint
			"[0-7]{1,3}|" .      // one to three octal digit ascii char code
			"x[\dA-Fa-f]{2}|" .  // two hex digit ascii char code
			"U[\dA-Fa-f]{8}" .   // eight hex digit unicode codepoint
			")|" .
			"&[a-zA-Z\d]{2,};";  // named character entity
	
	
		/**
		 * D integer number literals
		 *
		 * @type {Object}
		 */
		$D_INTEGER_MODE = new Mode(array(
			"className" => "number",
			"begin" => "\b" . $integer_re . "(L|u|U|Lu|LU|uL|UL)?",
	 		"relevance" => 0
		));
	
		/**
		 * [$D_FLOAT_MODE description]
		 * @type {Object}
		 */
		$D_FLOAT_MODE = new Mode(array(
			"className" => "number",
			"begin" => "\b(" .
				$float_re . "([fF]|L|i|[fF]i|Li)?|" .
				$integer_re . "(i|[fF]i|Li)" .
				")",
			"relevance" => 0
		));
	
		/**
		 * D character literal
		 *
		 * @type {Object}
		 */
		$D_CHARACTER_MODE = new Mode(array(
			"className" => "string",
			"begin" => "'(" . $escape_sequence_re . "|.)", 
			"end" => "'",
			"illegal" => "\."
		));
	
		/**
		 * D string escape sequence
		 *
		 * @type {Object}
		 */
		$D_ESCAPE_SEQUENCE = new Mode(array(
			"begin" => $escape_sequence_re,
			"relevance" => 0
		));
	
		/**
		 * D double quoted string literal
		 *
		 * @type {Object}
		 */
		$D_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "\"",
			"contains" => array($D_ESCAPE_SEQUENCE),
			"end" => "\"[cwd]?",
			"relevance" => 0
		));
	
		/**
		 * D wysiwyg and delimited string literals
		 *
		 * @type {Object}
		 */
		$D_WYSIWYG_DELIMITED_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "[rq]\"",
			"end" => "\"[cwd]?",
			"relevance" => 5
		));
	
		/**
		 * D alternate wysiwyg string literal
		 *
		 * @type {Object}
		 */
		$D_ALTERNATE_WYSIWYG_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "`",
			"end" => "`[cwd]?"
		));
	
		/**
		 * D hexadecimal string literal
		 *
		 * @type {Object}
		 */
		$D_HEX_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "x\"[\da-fA-F\s\n\r]*\"[cwd]?",
			"relevance" => 10
		));
	
		/**
		 * D delimited string literal
		 *
		 * @type {Object}
		 */
		$D_TOKEN_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "q\"\{",
			"end" => "\}\""
		));
	
		/**
		 * Hashbang support
		 *
		 * @type {Object}
		 */
		$D_HASHBANG_MODE = new Mode(array(
			"className" => "shebang",
			"begin" => "^#!",
			"end" => "$",
			"relevance" => 5
		));
	
		/**
		 * D special token sequence
		 *
		 * @type {Object}
		 */
		$D_SPECIAL_TOKEN_SEQUENCE_MODE = new Mode(array(
			"className" => "preprocessor",
			"begin" => "#(line)",
			"end" => "$",
			"relevance" => 5
		));
	
		/**
		 * D attributes
		 *
		 * @type {Object}
		 */
		$D_ATTRIBUTE_MODE = new Mode(array(
			"className" => "keyword",
			"begin" => "@[a-zA-Z_][a-zA-Z_\d]*"
		));
	
		/**
		 * D nesting comment
		 *
		 * @type {Object}
		 */
		$D_NESTING_COMMENT_MODE = new Mode(array(
			"className" => "comment",
			"begin" => "\/\+",
			"contains" => array("self"),
			"end" => "\+\/",
			"relevance" => 10
		));
	
		return array(
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$D_NESTING_COMMENT_MODE,
			$D_HEX_STRING_MODE,
			$D_STRING_MODE,
			$D_WYSIWYG_DELIMITED_STRING_MODE,
			$D_ALTERNATE_WYSIWYG_STRING_MODE,
			$D_TOKEN_STRING_MODE,
			$D_FLOAT_MODE,
			$D_INTEGER_MODE,
			$D_CHARACTER_MODE,
			$D_HASHBANG_MODE,
			$D_SPECIAL_TOKEN_SEQUENCE_MODE,
			$D_ATTRIBUTE_MODE
		);
	}
}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Stephan Kountso aka StepLg (steplg@gmail.com), highlight.js
 *              (original author)
 *              Evgeny Stepanischev (imbolk@gmail.com), highlight.js
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
 * This file is a direct port of go.js, the Go language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Go extends Language {
	
	protected function getName() {
		return "go";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"break default func interface select case map struct chan else goto package switch " .
				"const fallthrough if range type continue for import return var go defer",
			"constant" =>
				"true false iota nil",
			"typename" =>
				"bool byte complex64 complex128 float32 float64 int8 int16 int32 int64 string uint8 " .
				"uint16 uint32 uint64 int uint uintptr rune",
			"built_in" =>
				"append cap close complex copy imag len make new panic print println real recover delete"
		); 
	}
	
	protected function getIllegal() {
		return "<\/";
	}

	protected function getContainedModes() {
	
		return array(
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "string",
				"begin" => "'", 
				"end" => "[^\\\\]'",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "`", 
				"end" => "`"
			)),
			new Mode(array(
				"className" => "number",
				"begin" => "[^a-zA-Z_0-9](\-|\+)?\d+(\.\d+|\/\d+)?((d|e|f|l|s)(\+|\-)?\d+)?",
				"relevance" => 0
			)),
			$this->C_NUMBER_MODE
		);
	}

}

?>
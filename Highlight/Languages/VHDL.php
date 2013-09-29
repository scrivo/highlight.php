<?php
/* Copyright (c)
 * - 2006-2013, Igor Kalnitsky (igor@kalnitsky.org), highlight.js
 *              (original author)
 * - 2006-2013, Daniel C.K. Kho (daniel.kho@gmail.com), highlight.js
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
 * This file is a direct port of vhdl.js, the VHDL language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class VHDL extends Language {
	
	protected function getName() {
		return "vhdl";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getIllegal() {
		return "{";
	}
		
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"abs access after alias all and architecture array assert attribute begin block " .
				"body buffer bus case component configuration constant context cover disconnect " .
				"downto default else elsif end entity exit fairness file for force function generate " .
				"generic group guarded if impure in inertial inout is label library linkage literal " .
				"loop map mod nand new next nor not null of on open or others out package port " .
				"postponed procedure process property protected pure range record register reject " .
				"release rem report restrict restrict_guarantee return rol ror select sequence " .
				"severity shared signal sla sll sra srl strong subtype then to transport type " .
				"unaffected units until use variable vmode vprop vunit wait when while with xnor xor",
			"typename" =>
				"boolean bit character severity_level integer time delay_length natural positive " .
				"string bit_vector file_open_kind file_open_status std_ulogic std_ulogic_vector " .
				"std_logic std_logic_vector unsigned signed boolean_vector integer_vector " .
				"real_vector time_vector"
		);
	}

	protected function getContainedModes() {
				
		return array(
			$this->C_BLOCK_COMMENT_MODE, // VHDL-2008 block commenting.
			new Mode(array(
				"className" => "comment",
				"begin" => "--", 
				"end" => "$"
			)),
			$this->QUOTE_STRING_MODE,
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "literal",
				"begin" => "'(U|X|0|1|Z|W|L|H|-)'",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			new Mode(array(
				"className" => "attribute",
				"begin" => "'[A-Za-z](_?[A-Za-z0-9])*",
				"contains" => array($this->BACKSLASH_ESCAPE)
			))
		);
		
	}

}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Evgeny Stepanischev (imbolk@gmail.com), highlight.js
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
 * This file is a direct port of cpp.js, the C++ language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class CPP extends Language {
	
	protected function getName() {
		return "cpp";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" => 
				"false int float while private char catch export virtual operator sizeof " .
				"dynamic_cast|10 typedef const_cast|10 const struct for static_cast|10 union namespace " .
				"unsigned long throw volatile static protected bool template mutable if public friend " .
				"do return goto auto void enum else break new extern using true class asm case typeid " .
				"short reinterpret_cast|10 default double register explicit signed typename try this " .
				"switch continue wchar_t inline delete alignof char16_t char32_t constexpr decltype " .
				"noexcept nullptr static_assert thread_local restrict _Bool complex",
			"built_in" => 
				"std string cin cout cerr clog stringstream istringstream ostringstream " .
				"auto_ptr deque list queue stack vector map set bitset multiset multimap unordered_set " .
				"unordered_map unordered_multiset unordered_multimap array shared_ptr"
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
				"begin" => "'\\\\?.", 
				"end" => "'",
				"illegal" => "."
			)),
			new Mode(array(
				"className" => "number",
				"begin" => "\b(\d+(\.\d*)?|\.\\d+)(u|U|l|L|ul|UL|f|F)"
			)),
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#", 
				"end" => "$",
				"contains" => array(
					new Mode(array(
						"begin" => "<", 
						"end" => ">", 
						"illegal" => "\n"
					)),
					$this->C_LINE_COMMENT_MODE
				)
			)),
			new Mode(array(
				"className" => "stl_container",
				"begin" => "\b(deque|list|queue|stack|vector|map|set|bitset|multiset|multimap|unordered_map|unordered_set|unordered_multiset|unordered_multimap|array)\s*<", 
				"end" => ">",
				"keywords" => $this->getKeyWords(),
				"relevance" => 10,
				"contains" => array(
					"self"
				)
			))
		);
	}

}

?>
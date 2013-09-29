<?php
/* Copyright (c)
 * - 2006-2013, Dmitri Roudakov (dmitri@roudakov.ru), highlight.js
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
 * This file is a direct port of cmake.js, the CMake language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Axapta extends Language {
	
	protected function getName() {
		return "axapta";
	}
	
	protected function getKeyWords() {
		return
			"false int abstract private char boolean static null if for true " .
			"while long throw finally protected final return void enum else " .
			"break new catch byte super case short default double public try this switch " .
			"continue reverse firstfast firstonly forupdate nofetch sum avg minof maxof count " .
			"order group by asc desc index hint like dispaly edit client server ttsbegin " .
			"ttscommit str real date container anytype common div mod";
	}

	protected function getContainedModes() {
				
		return array(
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$this->APOS_STRING_MODE,
			$this->QUOTE_STRING_MODE,
			$this->C_NUMBER_MODE,
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "class",
				"beginWithKeyword" => true, 
				"end" => "{",
				"illegal" => ":",
				"keywords" => "class interface",
				"contains" => array(
					new Mode(array(
						"className" => "inheritance",
						"beginWithKeyword" => true,
						"keywords" => "extends implements",
						"relevance" => 10
					)),
					new Mode(array(
						"className" => "title",
						"begin" => $this->UNDERSCORE_IDENT_RE
					))
				)
			))
		);
	}

}

?>
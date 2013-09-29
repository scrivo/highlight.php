<?php
/* Copyright (c)
 * - 2006-2013, Leonov (gojpeg@yandex.ru), highlight.js (original author)
 *              Ivan Sagalaev (maniac@softwaremaniacs.org)
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
 * This file is a direct port of nginx.js, the Nginx language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Nginx extends Language {
	
	protected function getName() {
		return "nginx";
	}
	
	protected function getIllegal() {
		return "[^\s\}]";
	}
	
	protected function getKeyWords() {
		return null;
	}
	
	protected function getContainedModes() {
		
		$VARS = array(
			new Mode(array(
				"className" => "variable", 
				"begin" => "\$\d+"
			)),
			new Mode(array(
				"className" => "variable", 
				"begin" => "\${", 
				"end" => "}"
			)),
			new Mode(array(
				"className" => "variable", 
				"begin" => "[\$\@]" . $this->UNDERSCORE_IDENT_RE
			))
		);
		
		$DEFAULT = new Mode(array(
			"endsWithParent" => true,
			"lexems" => "[a-z\/_]+",
			"keywords" => array(
				"built_in" =>
					"on off yes no true false none blocked debug info notice warn error crit " .
					"select break last permanent redirect kqueue rtsig epoll poll /dev/poll"
			),
			"relevance" => 0,
			"illegal" => "=>",
			"contains" => array_merge(
				array(
					$this->HASH_COMMENT_MODE,
					new Mode(array(
						"className" => "string",
						"begin" => "\"", 
						"end" => "\"",
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS),
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "string",
						"begin" => "'", 
						"end" => "'",
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS),
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "url",
						"begin" => "([a-z]+):\/", 
						"end" => "\s", 
						"endsWithParent" => true, 
						"excludeEnd" => true
					)),
					new Mode(array(
						"className" => "regexp",
						"begin" => "\s\^", 
						"end" => "\s|{|;", 
						"returnEnd" => true,
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS)
					)),
					// regexp locations (~, ~*)
					new Mode(array(
						"className" => "regexp",
						"begin" => "~\*?\s+", 
						"end" => "\s|{|;", 
						"returnEnd" => true,
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS)
					)),
					// *.example.com
					new Mode(array(
						"className" => "regexp",
						"begin" => "\*(\.[a-z\-]+)+",
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS)
					)),
					// sub.example.*
					new Mode(array(
						"className" => "regexp",
						"begin" => "([a-z\-]+\.)+\*",
						"contains" => array_merge(array($this->BACKSLASH_ESCAPE), $VARS)
					)),
					// IP
					new Mode(array(
						"className" => "number",
						"begin" => "\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(:\d{1,5})?\b"
					)),
					// units
					new Mode(array(
						"className" => "number",
						"begin" => "\b\d+[kKmMgGdshdwy]*\b",
						"relevance" => 0
					))
				),
				$VARS
			)
		));
		
		return array(
			$this->HASH_COMMENT_MODE,
			new Mode(array(
				"begin" => $this->UNDERSCORE_IDENT_RE . "\s", 
				"end" => ";|{", 
				"returnBegin" => true,
				"contains" => array(
					new Mode(array(
						"className" => "title",
						"begin" => $this->UNDERSCORE_IDENT_RE,
						"starts" => $DEFAULT
					))
				),
				"relevance" => 0
			))
		);
		
	}

}

?>
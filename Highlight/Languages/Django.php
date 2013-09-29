<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
 *              (original author)
 * - 2006-2013, Ilya Baryshev (baryshev@gmail.com), highlight.js
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
 * This file is a direct port of django.js, the Django language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Django extends XML {

	protected function getName() {
		return "django";
	}
	
	private function getFilter() {
		return new Mode(array(
			"className" => "filter",
			"begin" => "\|[A-Za-z]+\:?",
			"keywords" =>
				"truncatewords removetags linebreaksbr yesno get_digit timesince random striptags " .
				"filesizeformat escape linebreaks length_is ljust rjust cut urlize fix_ampersands " .
				"title floatformat capfirst pprint divisibleby add make_list unordered_list urlencode " .
				"timeuntil urlizetrunc wordcount stringformat linenumbers slice date dictsort " .
				"dictsortreversed default_if_none pluralize lower join center default " .
				"truncatewords_html upper length phone2numeric wordwrap time addslashes slugify first " .
				"escapejs force_escape iriencode last safe safeseq truncatechars localize unlocalize " .
				"localtime utc timezone",
			"contains" => array(
				new Mode(array(
					"className" => "argument", 
					"begin" => "\"", 
					"end" => "\""
				))
			)
		));
	}	
	
	private function getDjangoContains() {
		return array(
			new Mode(array(
				"className" => "template_comment",
				"begin" => "{%\s*comment\s*%}", 
				"end" => "{%\s*endcomment\s*%}"
			)),
			new Mode(array(
				"className" => "template_comment",
				"begin" => "{#", 
				"end" => "#}"
			)),
			new Mode(array(
				"className" => "template_tag",
				"begin" => "{%", 
				"end" => "%}",
				"keywords" =>
					"comment endcomment load templatetag ifchanged endifchanged if endif firstof for " .
					"endfor in ifnotequal endifnotequal widthratio extends include spaceless " .
					"endspaceless regroup by as ifequal endifequal ssi now with cycle url filter " .
					"endfilter debug block endblock else autoescape endautoescape csrf_token empty elif " .
					"endwith static trans blocktrans endblocktrans get_static_prefix get_media_prefix " .
					"plural get_current_language language get_available_languages " .
					"get_current_language_bidi get_language_info get_language_info_list localize " .
					"endlocalize localtime endlocaltime timezone endtimezone get_current_timezone " .
					"verbatim",
				"contains" => array($this->getFilter())
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "{{", 
				"end" => "}}",
				"contains" => array($this->getFilter())
			))
		);
	}
	
	private function allowsDjangoSyntax($mode, $parent) {
		return (
			!$parent || // default mode
			(!$mode->className && $parent->className == "tag") || // tag_internal
			$mode->className == "value" // value
		);
	}
	
	private function copy($mode, $parent=null) {
		$contains = array();
		for ($i=0; isset($mode->contains) && $i<count($mode->contains); $i++) {
			$contains[] = $this->copy($mode->contains[$i], $mode);
		}
		if ($this->allowsDjangoSyntax($mode, $parent)) {
			$contains = array_merge($this->getDjangoContains(), $contains);
		}
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
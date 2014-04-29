<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniacsoftwaremaniacs.org), highlight.js
 *              (original author)
 * - 2013,      Geert Bergman (geertscrivo.nl), highlight.php
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

namespace Highlight;

class Language {

	protected $IDENT_RE = "[a-zA-Z][a-zA-Z0-9_]*";

	public $caseInsensitive = false;
	
	public function complete(&$e) {

		$patch = array(
			"begin" => true,
			"end" => true,
			"lexems" => true,
			"illegal" => true,
		);

		$def = array(
			"begin" => "",
			"beginRe" => "",
			"beginWithKeyword" => "",
			"excludeBegin" => "",
			"returnBegin" => "",
			"end" => "",
			"endRe" => "",
			"endsWithParent" => "",
			"excludeEnd" => "",
			"returnEnd" => "",
			"starts" => "",
			"terminators" => "",
			"terminatorEnd" => "",
			"lexems" => "",
			"lexemsRe" => "",
			"illegal" => "",
			"illegalRe" => "",
			"className" => "",
			"contains" => array(),
			"keywords" => null,
			"subLanguage" => null,
			"compiled" => false,
			"relevance" => 1);

		foreach($patch as $k =>  $v) {
			if (isset($e->$k)) {
				$e->$k = str_replace("\\/", "/", $e->$k);
				$e->$k = str_replace("/", "\\/", $e->$k);
			}
		}

		foreach($def as $k =>  $v) {
			if (!isset($e->$k)) {
				@$e->$k = $v;
			}
		}
		
	}

	public $mode = null;
	
	public function __construct($lang) {

		$json = file_get_contents(dirname(__FILE__)."/languages/{$lang}.json");
		$jr = new JsonRef();
		$this->mode = $jr->decode($json);
		
		$this->name = $lang;

		$this->caseInsensitive = isset($this->mode->case_insensitive) ?
			$this->mode->case_insensitive : false;
		
		$this->compile();
	}
	
	private function langRe($value, $global=false) {
		return "/{$value}/um" . ($this->caseInsensitive ? "i" : "");
	}

	private function processKeyWords($kw) {
		if (is_string($kw)) {
			if ($this->caseInsensitive) {
				$kw = mb_strtolower($kw, "UTF-8");
			}
			$kw = array("keyword" => explode(" ", $kw));
		} else {
			foreach($kw as $cls=>$vl) {
				if (!is_array($vl)) {
					if ($this->caseInsensitive) {
						$vl = mb_strtolower($vl, "UTF-8");
					}
					$kw->$cls = explode(" ", $vl);
				}
			}
		}
		return $kw;
	}

	private function compileMode(&$mode, $parent=null) {

		if (isset($mode->compiled)) {
			return;
		}
		$this->complete($mode);
		$mode->compiled = true;
		
		$kwds = array(); // used later with beginWithKeyword but filled as a side-effect of keywords compilation

		if ($mode->keywords) {

			$compiledKeywords = array();

			$mode->lexemsRe = $this->langRe($mode->lexems ? $mode->lexems : "\b". $this->IDENT_RE . "\b(?!\.)", true);

			foreach($this->processKeyWords($mode->keywords) as $className => $data) {
				if (!is_array($data)) {
					$data = array($data);
				}
				foreach ($data as $kw) {
					$pair = explode("|", $kw);
					$compiledKeywords[$pair[0]] = array($className,
							isset($pair[1]) ? intval($pair[1]) : 1);
					$kwds[] = $pair[0];
				}
			}
			$mode->keywords = $compiledKeywords;
		}

		if ($parent) {
			if ($mode->beginWithKeyword) {
				$mode->begin = "\b(" . implode("|", $kwds) . ")\b(?!\.)\s*";
			}
			$mode->beginRe = $this->langRe($mode->begin ? $mode->begin : "\B|\b");
			if (!$mode->end && !$mode->endsWithParent) {
				$mode->end = "\B|\b";
			}
			if ($mode->end) {
				$mode->endRe = $this->langRe($mode->end);
			}
			$mode->terminatorEnd = $mode->end;
			if ($mode->endsWithParent && $parent->terminatorEnd) {
				$mode->terminatorEnd .= ($mode->end ? "|" : "") . $parent->terminatorEnd;
			}
		}

		if ($mode->illegal) {
			$mode->illegalRe = $this->langRe($mode->illegal);
		}

		for ($i=0; $i<count($mode->contains); $i++) {
			if ("self" === $mode->contains[$i]) {
				$mode->contains[$i] = $mode;
			} 
			$this->compileMode($mode->contains[$i], $mode);
		}
				
		if ($mode->starts) {
			$this->compileMode($mode->starts, $parent);
		}
		
		$terminators = array();

		for ($i=0; $i<count($mode->contains); $i++) {
			$terminators[] = $mode->contains[$i]->begin;
		}
		if ($mode->terminatorEnd) {
			$terminators[] = $mode->terminatorEnd;
		}
		if ($mode->illegal) {
			$terminators[] = $mode->illegal;
		}
		$mode->terminators = count($terminators)
			? $this->langRe(implode("|", $terminators), true) : null;
	}

	protected function compile() {
		$this->compileMode($this->mode);
	}

}

?>
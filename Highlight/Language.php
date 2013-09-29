<?php
/* Copyright (c)
 * - 2006-2013, Ivan Sagalaev (maniac@softwaremaniacs.org), highlight.js
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

namespace Highlight;

abstract class Language extends Mode {

	// Common regexps
	protected $IDENT_RE = "[a-zA-Z][a-zA-Z0-9_]*";
	protected $UNDERSCORE_IDENT_RE = "[a-zA-Z_][a-zA-Z0-9_]*";
	protected $NUMBER_RE = "\b\d+(\.\d+)?";
	protected $C_NUMBER_RE = // 0x..., 0..., decimal, float
		"(\b0[xX][a-fA-F0-9]+|(\b\d+(\.\d*)?|\.\d+)([eE][-+]?\\d+)?)"; 
	protected $BINARY_NUMBER_RE = "\b(0b[01]+)"; // 0b...
	protected $RE_STARTERS_RE = 
		"!|!=|!==|%|%=|&|&&|&=|\*|\*=|\+|\+=|,|\.|-|-=|\/|\/=|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\?|\[|\{|\(|\^|\^=|\||\|=|\|\||~";

	// Common modes
	protected $BACKSLASH_ESCAPE;
	protected $APOS_STRING_MODE;
	protected $QUOTE_STRING_MODE;
	protected $C_LINE_COMMENT_MODE;
	protected $C_BLOCK_COMMENT_MODE;
	protected $HASH_COMMENT_MODE;
	protected $NUMBER_MODE;
	protected $C_NUMBER_MODE;
	protected $BINARY_NUMBER_MODE;
	protected $REGEXP_MODE;

	protected $caseInsinsitive = false;
	
	public function __construct() {

		parent::__construct();

		$this->initCommonModes();
		
		$this->caseInsensitive = $this->isCaseInsensitive();
		$this->name = $this->getName();
		$this->keywords = $this->getKeyWords();
		$this->contains = $this->getContainedModes();
		$this->illegal = $this->getIllegal();
		$this->lexems = $this->getLexems();
		$sl = $this->getSubLanguage();
		if ($sl instanceof Mode) {
			$this->subLanguage = $sl->subLanguage;
			$this->relevance = $sl->relevance;
		} else {
			$this->subLanguage = $sl;
		}
		
		$this->compile();
	}
	
	protected function isCaseInsensitive() {
		return false;
	}
	
	abstract protected function getName();
	
	abstract protected function getKeyWords();
	
	abstract protected function getContainedModes();

	protected function getIllegal() {
		return null;
	}
	
	protected function getLexems() {
		return null;
	}
	
	protected function getSubLanguage() {
		return null;
	}
	
	private function initCommonModes() {

		// Common modes
		$this->BACKSLASH_ESCAPE = new Mode(array(
			"begin" => "\\\\[\s\S]",
			"relevance" => 0
		));

		$this->APOS_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "'",
			"end" => "'",
			"illegal" => "\\n", // < no multiline strings
			"contains" => array($this->BACKSLASH_ESCAPE),
			"relevance" => 0
		));

		$this->QUOTE_STRING_MODE = new Mode(array(
			"className" => "string",
			"begin" => "\"",
			"end" => "\"",
			"illegal" => "\\n", // < no multiline strings
			"contains" => array($this->BACKSLASH_ESCAPE),
			"relevance" => 0
		));

		$this->C_LINE_COMMENT_MODE = new Mode(array(
			"className" => "comment",
			"begin" => "\/\/",
			"end" => "$"
		));

		$this->C_BLOCK_COMMENT_MODE = new Mode(array(
			"className" => "comment",
			"begin" => "\/\*",
			"end" => "\*\/"
		));

		$this->HASH_COMMENT_MODE = new Mode(array(
			"className" => "comment",
			"begin" => "#",
			"end" => "$"
		));

		$this->NUMBER_MODE = new Mode(array(
			"className" => "number",
			"begin" => $this->NUMBER_RE,
			"relevance" => 0
		));

		$this->C_NUMBER_MODE = new Mode(array(
			"className" => "number",
			"begin" => $this->C_NUMBER_RE,
			"relevance" => 0
		));

		$this->BINARY_NUMBER_MODE = new Mode(array(
			"className" => "number",
			"begin" => $this->BINARY_NUMBER_RE,
			"relevance" => 0
		));

		$this->REGEXP_MODE = new Mode(array(
			"className" => "regexp",
			"begin" => "\/",
			"end" => "\/[im]*",
			"illegal" => "\n",
			"contains" => array(
				$this->BACKSLASH_ESCAPE,
				new Mode(array(
					"begin" => "\[",
					"end" => "\]",
					"relevance" => 0,
					"contains" => array($this->BACKSLASH_ESCAPE)
				))
			)
		));
	}

	private function langRe($value, $global=false) {
		return "/{$value}/um" . ($this->caseInsensitive ? "i" : "");
	}

	private function processKeyWords($kw) {
		if (is_string($kw)) {
			$kw = array("keyword" => explode(" ", $kw));
		} else {
			foreach($kw as $cls=>$vl) {
				$kw[$cls] = explode(" ", $vl);
			}
		}
		return $kw;
	}

	private function compileMode($mode, $parent=null) {

		if ($mode->compiled) {
			return;
		}
		$mode->compiled = true;

		$kwds = array(); // used later with beginWithKeyword but filled as a side-effect of keywords compilation

		if ($mode->keywords) {

			$compiledKeywords = array();

			$mode->lexemsRe = $this->langRe($mode->lexems ? $mode->lexems : $this->IDENT_RE . "(?!\\.)", true);

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
		$this->compileMode($this);
	}

}

?>
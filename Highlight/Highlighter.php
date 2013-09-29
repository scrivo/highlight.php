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

class Highlighter {

	private $modeBuffer = "";
	private $result = "";
	private $top = null;
	private $language = null;
	private $keywordCount = 0;
	private $relevance = 0;
	private $ignoreIllegals = false;
	
	private $classMap = array(
		"python" => "Python",
		"profile" => "PythonProfiler",
		"ruby" => "Ruby",
		"haml" => "Haml",
		"perl" => "Perl",
		"php" => "PHP",
		"scala" => "Scala",
		"go" => "Go",
		"xml" => "XML",
		"html" => "XML",
		"lasso" => "Lasso",
		"markdown" => "Markdown",
		"asciidoc" => "AsciiDoc",
		"django" => "Django",
		"handlebars" => "Handlebars",
		"css" => "CSS",
		"scss" => "SCSS",
		"json" => "JSON",
		"javascript" => "JavaScript",
		"coffeescript" => "CoffeeScript",
		"actionscript" => "ActionScript",
		"vbscript" => "VBScript",
		"vbnet" => "VBNet",
		"http" => "HTTP",
		"lua" => "Lua",
		"applescript" => "AppleScript",
		"delphi" => "Delphi",
		"java" => "Java",
		"cpp" => "CPP",
		"objectivec" => "ObjectiveC",
		"vala" => "Vala",
		"cs" => "CSharp",
		"fsharp" => "FSharp",
		"d" => "D",
		"rsl" => "RSL",
		"rib" => "RIB",
		"mel" => "MEL",
		"glsl" => "GLSL",
		"sql" => "SQL",
		"smalltalk" => "SmallTalk",
		"lisp" => "Lisp",
		"clojure" => "Clojure",
		"ini" => "INI",
		"apache" => "Apache",
		"nginx" => "Nginx",
		"diff" => "Diff",
		"dos" => "DOS",
		"bash" => "Bash",
		"cmake" => "CMake",
		"axapta" => "Axapta",
		"ruleslanguage" => "OracleRL",
		"1c" => "OneC",
		"avrasm" => "AvrASM",
		"vhdl" => "VHDL",
		"parser3" => "Parser3",
		"tex" => "TeX",
		"brainfuck" => "Brainfuck",
		"haskell" => "Haskell",
		"erlang" => "Erlang",
		"erlang-repl" => "ErlangREPL",
		"matlab" => "Matlab",
		"rust" => "Rust",
		"r" => "R",
		"mizar" => "Mizar");
	
	private $autodetectSet = array(
		"html", "xml", "json", "javascript", "css", "php", "http"
	);
	
	private function createLanguage($languageId) {
		if (is_string($this->classMap[$languageId])) {
			$lang = "Highlight\\Languages\\{$this->classMap[$languageId]}";
			$this->classMap[$languageId] = new $lang();
		}
		return $this->classMap[$languageId];
	}

	private function subMode($lexem, $mode) {
		for ($i=0; $i<count($mode->contains); $i++) {
			if (preg_match($mode->contains[$i]->beginRe, $lexem, $match, PREG_OFFSET_CAPTURE)) {
				if ($match[0][1] == 0) {
					return $mode->contains[$i];
				}
			}
		}
	}

	private function endOfMode($mode, $lexem) {
		if ($mode->end && preg_match($mode->endRe, $lexem)) {
			return $mode;
		}
		if ($mode->endsWithParent) {
			return $this->endOfMode($mode->parent, $lexem);
		}
	}

	private function isIllegal($lexem, $mode) {
		return !$this->ignoreIllegals && $mode->illegal && preg_match($mode->illegalRe, $lexem);
	}

	private function keywordMatch($mode, $match) {
		$kwd = $this->language->caseInsensitive ? mb_strtolower($match[0], "UTF-8") : $match[0];
		return isset($mode->keywords[$kwd]) ? $mode->keywords[$kwd] : null;
	}

	private function processKeywords() {

		$buffer = htmlspecialchars($this->modeBuffer, ENT_NOQUOTES);
		if (!$this->top->keywords) {
			return $buffer;
		}

		$result = "";
		$lastIndex = 0;
		while (preg_match($this->top->lexemsRe, $buffer, $match, PREG_OFFSET_CAPTURE, $lastIndex)) {

			$result .= substr($buffer, $lastIndex, $match[0][1] - $lastIndex);
			$keyword_match = $this->keywordMatch($this->top, $match[0]);

			if ($keyword_match) {
				$this->keywordCount += $keyword_match[1];
				$result .= "<span class=\"{$keyword_match[0]}\">{$match[0][0]}</span>";
			} else {
				$result .= $match[0][0];
			}

			$lastIndex = strlen($match[0][0]) + $match[0][1];
		}

		return $result . substr($buffer, $lastIndex);
	}

	private function processSubLanguage() {
		try {
			$hl = new Highlighter();
			$hl->autodetectSet = $this->autodetectSet;
			if ($this->top->subLanguage) {
				$res = $hl->highlight($this->top->subLanguage, $this->modeBuffer, $this->ignoreIllegals);
			} else {
				$res = $hl->highlightAuto($this->modeBuffer);
			}
			// Counting embedded language score towards the host language may be disabled
			// with zeroing the containing mode relevance. Usecase in point is Markdown that
			// allows XML everywhere and makes every XML snippet to have a much larger Markdown
			// score.
			if ($this->top->relevance > 0) {
				$this->keywordCount += $res->keywordCount;
				$this->relevance += $res->relevance;
			}
			return "<span class=\"{$res->language}\">{$res->value}</span>";
				
		} catch(\Exception $e) {
			return htmlspecialchars($this->modeBuffer, ENT_NOQUOTES);
		}

	}

	private function processBuffer() {
		return isset($this->top->subLanguage) ? $this->processSubLanguage() : $this->processKeywords();
	}

	private function startNewMode($mode, $lexem) {

		$markup = $mode->className ? "<span class=\"{$mode->className}\">" : "";

		if ($mode->returnBegin) {
			$this->result .= $markup;
			$this->modeBuffer = "";
		} else if ($mode->excludeBegin) {
			$this->result .= htmlspecialchars($lexem, ENT_NOQUOTES) . $markup;
			$this->modeBuffer = "";
		} else {
			$this->result .= $markup;
			$this->modeBuffer = $lexem;
		}

		$t = clone $mode;
		$t->parent = $this->top;
		$this->top = $t;
	}

	private function processLexem($buffer, $lexem=null) {

		$this->modeBuffer .= $buffer;
		if (null === $lexem) {
			$this->result .= $this->processBuffer();
			return 0;
		}

		$new_mode = $this->subMode($lexem, $this->top);
		if ($new_mode) {
			$this->result .= $this->processBuffer();
			$this->startNewMode($new_mode, $lexem);
			return $new_mode->returnBegin ? 0 : strlen($lexem);
		}

		$end_mode = $this->endOfMode($this->top, $lexem);
		if ($end_mode) {
			$origin = $this->top;
			if (!($origin->returnEnd || $origin->excludeEnd)) {
				$this->modeBuffer .= $lexem;
			}
			$this->result .= $this->processBuffer();
			do {
				if ($this->top->className) {
					$this->result .= "</span>";
				}
				$this->relevance += $this->top->relevance;
				$this->top = $this->top->parent;
			} while ($this->top != $end_mode->parent);
			if ($origin->excludeEnd) {
				$this->result .= htmlspecialchars($lexem, ENT_NOQUOTES);
			}
			$this->modeBuffer = "";
			if ($end_mode->starts) {
				$this->startNewMode($end_mode->starts, "");
			}
			return $origin->returnEnd ? 0 : strlen($lexem);
		}

		if ($this->isIllegal($lexem, $this->top)) {
			throw new \Exception("Illegal lexem \"{$lexem}\" for mode \"" .
				(isset($this->top->className) ? $this->top->className : "unnamed") . "\"");
		}

		//Parser should not reach this point as all types of lexems should be caught
		//earlier, but if it does due to some bug make sure it advances at least one
		//character forward to prevent infinite looping.

		$this->modeBuffer .= $lexem;
		$l = strlen($lexem);
		return $l ? $l : 1;
	}
	
	public function setAutodetectLanguages(array $set) {
		$this->autodetectSet = $set;
	}

	public function highlight($language, $code, $ignoreIllegals=true) {
		
		$this->language = $this->createLanguage($language);
		$this->top = $this->language;
		$this->modeBuffer = "";
		$this->relevance = 0;
		$this->keywordCount = 0;
		$this->result = "";
		$this->ignoreIllegals = $ignoreIllegals;
		
		$res = new \stdClass;
		$res->relevance = 0;
		$res->value = "";
		$res->keywordCount = 0;
		$res->language = "";
		
		try {
			$match = null;
			$count = 0;
			$index = 0;

			while ($this->top->terminators) {
				if (!preg_match($this->top->terminators, $code, $match, PREG_OFFSET_CAPTURE, $index)) {
					break;
				}
				$count = $this->processLexem(substr($code, $index, $match[0][1] - $index), $match[0][0]);
				$index = $match[0][1] + $count;
			}
			$this->processLexem(substr($code, $index));

			$res->relevance = $this->relevance;
			$res->keywordCount = $this->keywordCount;
			$res->value = $this->result;
			$res->language = $this->language->name;
			
			return $res;
			
		} catch (\Exception $e) {
			
			if (strpos($e->getMessage(), "Illegal") !== false) {
				$res->value = htmlspecialchars($code, ENT_NOQUOTES);
				return $res;
			} else {
				throw $e;
			}
		}

	}
	
	public function highlightAuto($code) {
		
		$res = new \stdClass;
		$res->relevance = 0;
		$res->value = "";
		$res->keywordCount = 0;
		$res->language = "";
		$scnd = clone $res;
		
		foreach ($this->autodetectSet as $l) {
			$tmp = $this->highlight($l, $code, false);
			if ($tmp->keywordCount + $tmp->relevance > $scnd->keywordCount + $scnd->relevance) {
				$scnd = $tmp;
			}
			if ($tmp->keywordCount + $tmp->relevance > $res->keywordCount + $res->relevance) {
				$scnd = $res;
				$res = $tmp;
			}
		}
		
		if ($scnd->language) {
			$res->secondBest = $scnd;
		}
		
		return $res;
	}
	
}

?>
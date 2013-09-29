<?php
/* Copyright (c)
 * - 2006-2013, Eric Knibbe (eric@lassosoft.com), highlight.js (original author)
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
 * This file is a direct port of lasso.js, the Lasso language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Lasso extends Language {
	
	private $LASSO_IDENT_RE = "[a-zA-Z_][a-zA-Z0-9_.]*|&[lg]t;";
	private $LASSO_START = "<\?(lasso(script)?|=)";
	
	protected function getName() {
		return "lasso";
	}
	
	protected function getLexems() {
		return $this->LASSO_IDENT_RE;
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return array(
			"literal" =>
				"true false none minimal full all infinity nan and or not " .
				"bw ew cn lt lte gt gte eq neq ft rx nrx",
			"built_in" =>
				"array date decimal duration integer map pair string tag xml null " .
				"list queue set stack staticarray local var variable data global " .
				"self inherited void",
			"keyword" =>
				"error_code error_msg error_pop error_push error_reset cache " .
				"database_names database_schemanames database_tablenames define_tag " .
				"define_type email_batch encode_set html_comment handle handle_error " .
				"header if inline iterate ljax_target link link_currentaction " .
				"link_currentgroup link_currentrecord link_detail link_firstgroup " .
				"link_firstrecord link_lastgroup link_lastrecord link_nextgroup " .
				"link_nextrecord link_prevgroup link_prevrecord log loop " .
				"namespace_using output_none portal private protect records referer " .
				"referrer repeating resultset rows search_args search_arguments " .
				"select sort_args sort_arguments thread_atomic value_list while " .
				"abort case else if_empty if_false if_null if_true loop_abort " .
				"loop_continue loop_count params params_up return return_value " .
				"run_children soap_definetag soap_lastrequest soap_lastresponse " .
				"tag_name ascending average by define descending do equals " .
				"frozen group handle_failure import in into join let match max " .
				"min on order parent protected provide public require skip " .
				"split_thread sum take thread to trait type where with yield"
		);
	}

	protected function getContainedModes() {
		
		$tmp1 = $this->APOS_STRING_MODE;
		$tmp1->illegal = null;
		$tmp2 = $this->QUOTE_STRING_MODE;
		$tmp2->illegal = null;
		
		return array(
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "\]|\?>",
				"relevance" => 0,
				"starts" => new Mode(array(
					"className" => "markup",
					"end" => "\[|" . $this->LASSO_START,
					"returnEnd" => true
				))
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "\[noprocess\]",
				"starts" => new Mode(array(
					"className" => "markup",
					"end" => "\[\/noprocess\]",
					"returnEnd" => true
				))
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "\[no_square_brackets|\[\/noprocess|" . $this->LASSO_START
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "\[",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "shebang",
				"begin" => "^#!.+lasso9\b",
				"relevance" => 10
			)),
			$this->C_LINE_COMMENT_MODE,
			new Mode(array(
				"className" => "javadoc",
				"begin" => "\/\*\*!", 
				"end" => "\*\/"
			)),
			$this->C_BLOCK_COMMENT_MODE,
			$this->C_NUMBER_MODE,
			$tmp1,
			$tmp2,
			new Mode(array(
				"className" => "string",
				"begin" => "`", 
				"end" => "`"
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "#\d+|[#$]" . $this->LASSO_IDENT_RE
			)),
			new Mode(array(
				"className" => "tag",
				"begin" => "::", 
				"end" => $this->LASSO_IDENT_RE
			)),
			new Mode(array(
				"className" => "attribute",
				"begin" => "\.\.\.|-" . $this->UNDERSCORE_IDENT_RE
			)),
			new Mode(array(
				"className" => "class",
				"beginWithKeyword" => true, 
				"keywords" => "define",
				"excludeEnd" => true, 
				"end" => "\(|=>",
				"contains" => array(
					new Mode(array(
						"className" => "title",
						"begin" => $this->UNDERSCORE_IDENT_RE . "=?"
					))	
				)
			))	
		);
		
	}

}

?>
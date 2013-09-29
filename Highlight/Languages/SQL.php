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

/**
 * This file is a direct port of sql.js, the SQL language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class SQL extends Language {
	
	protected function getName() {
		return "sql";
	}
	
	protected function isCaseInsensitive() {
		return true; 
	}
	
	protected function getKeyWords() {
		return null;
	}

	protected function getContainedModes() {
		
		return array(
			new Mode(array(
				"className" => "operator",
				"begin" => "(begin|end|start|commit|rollback|savepoint|lock|alter|create|drop|rename|call|delete|do|handler|insert|load|replace|select|truncate|update|set|show|pragma|grant)\b(?!:)", // negative look-ahead here is specifically to prevent stomping on SmallTalk
				"end" => ";", 
				"endsWithParent" => true,
				"keywords" => array(
					"keyword" => 
						"all partial global month current_timestamp using go revoke smallint " .
						"indicator end-exec disconnect zone with character assertion to add current_user " .
						"usage input local alter match collate real then rollback get read timestamp " .
						"session_user not integer bit unique day minute desc insert execute like ilike|2 " .
						"level decimal drop continue isolation found where constraints domain right " .
						"national some module transaction relative second connect escape close system_user " .
						"for deferred section cast current sqlstate allocate intersect deallocate numeric " .
						"public preserve full goto initially asc no key output collation group by union " .
						"session both last language constraint column of space foreign deferrable prior " .
						"connection unknown action commit view or first into float year primary cascaded " .
						"except restrict set references names table outer open select size are rows from " .
						"prepare distinct leading create only next inner authorization schema " .
						"corresponding option declare precision immediate else timezone_minute external " .
						"varying translation true case exception join hour default double scroll value " .
						"cursor descriptor values dec fetch procedure delete and false int is describe " .
						"char as at in varchar null trailing any absolute current_time end grant " .
						"privileges when cross check write current_date pad begin temporary exec time " .
						"update catalog user sql date on identity timezone_hour natural whenever interval " .
						"work order cascade diagnostics nchar having left call do handler load replace " .
						"truncate start lock show pragma exists number trigger if before after each row",
					"aggregate" => 
						"count sum min max avg"
				),
				"contains" => array(
					new Mode(array(
						"className" => "string",
						"begin" => "'", 
						"end" => "'",
						"contains" => array(
							$this->BACKSLASH_ESCAPE, 
							new Mode(array(
								"begin" => "''"
							))
						),
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "string",
						"begin" => "\"", 
						"end" => "\"",
						"contains" => array(
							$this->BACKSLASH_ESCAPE, 
							new Mode(array(
								"begin" => "\"\""
							))
						),
						"relevance" => 0
					)),
					new Mode(array(
						"className" => "string",
						"begin" => "`", 
						"end" => "`",
						"contains" => array(
							$this->BACKSLASH_ESCAPE
						)
					)),
					$this->C_NUMBER_MODE
				)
			)),
			$this->C_BLOCK_COMMENT_MODE,
			new Mode(array(
				"className" => "comment",
				"begin" => "--", 
				"end" => "$"
			))
		);
		
	}

}

?>
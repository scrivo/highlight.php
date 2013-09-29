<?php
/* Copyright (c)
 * - 2006-2013, mfornos, highlight.js (original author)
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
 * This file is a direct port of clojure.js, the Clojure language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Clojure extends Language {
	
	protected function getName() {
		return "clojure";
	}
	
	protected function getKeyWords() {
		return null; 
	}

	protected function getIllegal() {
		return "\S";
	}
	
	protected function getContainedModes() {
		
		$keywords = array(
			"built_in" =>
				// Clojure keywords
				"def cond apply if-not if-let if not not= = &lt; < > &lt;= <= >= == . / * - rem ".
				"quot neg? pos? delay? symbol? keyword? true? false? integer? empty? coll? list? ".
				"set? ifn? fn? associative? sequential? sorted? counted? reversible? number? decimal? ".
				"class? distinct? isa? float? rational? reduced? ratio? odd? even? char? seq? vector? ".
				"string? map? nil? contains? zero? instance? not-every? not-any? libspec? -> ->> .. . ".
				"inc compare do dotimes mapcat take remove take-while drop letfn drop-last take-last ".
				"drop-while while intern condp case reduced cycle split-at split-with repeat replicate ".
				"iterate range merge zipmap declare line-seq sort comparator sort-by dorun doall nthnext ".
				"nthrest partition eval doseq await await-for let agent atom send send-off release-pending-sends ".
				"add-watch mapv filterv remove-watch agent-error restart-agent set-error-handler error-handler ".
				"set-error-mode! error-mode shutdown-agents quote var fn loop recur throw try monitor-enter ".
				"monitor-exit defmacro defn defn- macroexpand macroexpand-1 for doseq dosync dotimes and or ".
				"when when-not when-let comp juxt partial sequence memoize constantly complement identity assert ".
				"peek pop doto proxy defstruct first rest cons defprotocol cast coll deftype defrecord last butlast ".
				"sigs reify second ffirst fnext nfirst nnext defmulti defmethod meta with-meta ns in-ns create-ns import ".
				"intern refer keys select-keys vals key val rseq name namespace promise into transient persistent! conj! ".
				"assoc! dissoc! pop! disj! import use class type num float double short byte boolean bigint biginteger ".
				"bigdec print-method print-dup throw-if throw printf format load compile get-in update-in pr pr-on newline ".
				"flush read slurp read-line subvec with-open memfn time ns assert re-find re-groups rand-int rand mod locking ".
				"assert-valid-fdecl alias namespace resolve ref deref refset swap! reset! set-validator! compare-and-set! alter-meta! ".
				"reset-meta! commute get-validator alter ref-set ref-history-count ref-min-history ref-max-history ensure sync io! ".
				"new next conj set! memfn to-array future future-call into-array aset gen-class reduce merge map filter find empty ".
				"hash-map hash-set sorted-map sorted-map-by sorted-set sorted-set-by vec vector seq flatten reverse assoc dissoc list ".
				"disj get union difference intersection extend extend-type extend-protocol int nth delay count concat chunk chunk-buffer ".
				"chunk-append chunk-first chunk-rest max min dec unchecked-inc-int unchecked-inc unchecked-dec-inc unchecked-dec unchecked-negate ".
				"unchecked-add-int unchecked-add unchecked-subtract-int unchecked-subtract chunk-next chunk-cons chunked-seq? prn vary-meta ".
				"lazy-seq spread list* str find-keyword keyword symbol gensym force rationalize"
		);
		
		$CLJ_IDENT_RE = "[a-zA-Z_0-9\!\.\?\-\+\*\/\<\=\>\&\#\$\';]+";
		$SIMPLE_NUMBER_RE = "[\s:\(\{]+\d+(\.\d+)?";
		
		$NUMBER = new Mode(array(
			"className" => "number", 
			"begin" => $SIMPLE_NUMBER_RE,
			"relevance" => 0
		));
		
		$STRING = new Mode(array(
			"className" => "string",
			"begin" => "\"", 
			"end" => "\"",
			"contains" => array(
				$this->BACKSLASH_ESCAPE
			),
			"relevance" => 0
		));
		
		$COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => ";", 
			"end" => "$",
			"relevance" => 0
		));
		
		$COLLECTION = new Mode(array(
			"className" => "collection",
			"begin" => "[\[\{]", 
			"end" => "[\]\}]"
		));
		
		$HINT = new Mode(array(
			"className" => "comment",
			"begin" => "\^" . $CLJ_IDENT_RE
		));
		
		$HINT_COL = new Mode(array(
			"className" => "comment",
			"begin" => "\^\{", 
			"end" => "\}"
		));
		
		$KEY = new Mode(array(
			"className" => "attribute",
			"begin" => "[:]" . $CLJ_IDENT_RE
		));
		
		$LIST = new Mode(array(
			"className" => "list",
			"begin" => "\(", 
			"end" => "\)"
		));
		
		$BODY = new Mode(array(
			"endsWithParent" => true,
			"keywords" => array(
				"literal" => "true false nil"
			),
			"relevance" => 0
		));
		
		$TITLE = new Mode(array(
			"keywords" => $keywords,
			"lexems" => $CLJ_IDENT_RE,
			"className" => "title", 
			"begin" => $CLJ_IDENT_RE,
			"starts" => $BODY
		));
		
		$LIST->contains = array(
			new Mode(array(
				"className" => "comment", 
				"begin" => "comment"
			)),
			$TITLE
		);
		
		$BODY->contains = array(
			$LIST, $STRING, $HINT, $HINT_COL, $COMMENT, $KEY, $COLLECTION, $NUMBER);
		$COLLECTION->contains = array(
			$LIST, $STRING, $HINT, $COMMENT, $KEY, $COLLECTION, $NUMBER);
		
		return array(
			$COMMENT,
			$LIST
		);
		
	}

}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Kelley van Evert (kelleyvanevert@gmail.com), highlight.js
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
 * This file is a direct port of mizar.js, the Mizar language definition file 
 * for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Mizar extends Language {
	
	protected function getName() {
		return "mizar";
	}
	
	protected function getKeyWords() {
		return
			"environ vocabularies notations constructors definitions registrations theorems schemes requirements " .
			"begin end definition registration cluster existence pred func defpred deffunc theorem proof " .
			"let take assume then thus hence ex for st holds consider reconsider such that and in provided of as from " .
			"be being by means equals implies iff redefine define now not or attr is mode suppose per cases set " .
			"thesis contradiction scheme reserve struct " .
			"correctness compatibility coherence symmetry assymetry reflexivity irreflexivity " .
			"connectedness uniqueness commutativity idempotence involutiveness projectivity";
	}

	protected function getContainedModes() {
		
		return array(
			new Mode(array(
				"className" => "comment",
				"begin" => "::", 
				"end" => "$"
			))
		);
		
	}

}

?>
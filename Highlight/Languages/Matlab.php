<?php
/* Copyright (c)
 * - 2006-2013, Denis Bardadym (bardadymchik@gmail.com), highlight.js
 *              (original author)
 * - 2006-2013, Eugene Nizhibitsky (nizhibitsky@ya.ru), highlight.js
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
 * This file is a direct port of matlab.js, the Matlab language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Matlab extends Language {
	
	protected function getName() {
		return "matlab";
	}
	
	protected function getIllegal() {
		return "(\/\/|\"|#|\/\*|\s+\/\w+)";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"break case catch classdef continue else elseif end enumerated events for function " .
				"global if methods otherwise parfor persistent properties return spmd switch try while",
			"built_in" =>
				"sin sind sinh asin asind asinh cos cosd cosh acos acosd acosh tan tand tanh atan " .
				"atand atan2 atanh sec secd sech asec asecd asech csc cscd csch acsc acscd acsch cot " .
				"cotd coth acot acotd acoth hypot exp expm1 log log1p log10 log2 pow2 realpow reallog " .
				"realsqrt sqrt nthroot nextpow2 abs angle complex conj imag real unwrap isreal " .
				"cplxpair fix floor ceil round mod rem sign airy besselj bessely besselh besseli " .
				"besselk beta betainc betaln ellipj ellipke erf erfc erfcx erfinv expint gamma " .
				"gammainc gammaln psi legendre cross dot factor isprime primes gcd lcm rat rats perms " .
				"nchoosek factorial cart2sph cart2pol pol2cart sph2cart hsv2rgb rgb2hsv zeros ones " .
				"eye repmat rand randn linspace logspace freqspace meshgrid accumarray size length " .
				"ndims numel disp isempty isequal isequalwithequalnans cat reshape diag blkdiag tril " .
				"triu fliplr flipud flipdim rot90 find sub2ind ind2sub bsxfun ndgrid permute ipermute " .
				"shiftdim circshift squeeze isscalar isvector ans eps realmax realmin pi i inf nan " .
				"isnan isinf isfinite j why compan gallery hadamard hankel hilb invhilb magic pascal " .
				"rosser toeplitz vander wilkinson"
		);
	}

	protected function getContainedModes() {
		
		$COMMON_CONTAINS = array(
			$this->C_NUMBER_MODE,
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
			))
		);
		
		return array_merge(
			array(
				new Mode(array(
					"className" => "function",
					"beginWithKeyword" => true, 
					"end" => "$",
					"keywords" => "function",
					"contains" => array(
						new Mode(array(
							"className" => "title",
							"begin" => $this->UNDERSCORE_IDENT_RE
						)),
						new Mode(array(
							"className" => "params",
							"begin" => "\(", 
							"end" => "\)"
						)),
						new Mode(array(
							"className" => "params",
							"begin" => "\[", 
							"end" => "\]"
						))
					)
				)),
				new Mode(array(
					"className" => "transposed_variable",
					"begin" => "[a-zA-Z_][a-zA-Z_0-9]*('+[\.']*|[\.']+)", 
					"end" => ""
				)),
				new Mode(array(
					"className" => "matrix",
					"begin" => "\[", 
					"end" => "\]'*[\.']*",
					"contains" => $COMMON_CONTAINS
				)),
				new Mode(array(
					"className" => "cell",
					"begin" => "\{", 
					"end" => "\}'*[\.']*",
					"contains" => $COMMON_CONTAINS
				)),
				new Mode(array(
					"className" => "comment",
					"begin" => "\%", 
					"end" => "$"
				))
			),
			$COMMON_CONTAINS
		);
		
	}

}

?>
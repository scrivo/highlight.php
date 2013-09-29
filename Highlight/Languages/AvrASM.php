<?php
/* Copyright (c)
 * - 2006-2013, Vladimir Ermakov (vooon341@gmail.com), highlight.js
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
 * This file is a direct port of avrasm.js, the Avr assembly language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class AvrASM extends Language {
	
	protected function getName() {
		return "avrasm";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
		
	protected function getKeyWords() {
		return array(
			"keyword" =>
				/* mnemonic */
				"adc add adiw and andi asr bclr bld brbc brbs brcc brcs break breq brge brhc brhs " .
				"brid brie brlo brlt brmi brne brpl brsh brtc brts brvc brvs bset bst call cbi cbr " .
				"clc clh cli cln clr cls clt clv clz com cp cpc cpi cpse dec eicall eijmp elpm eor " .
				"fmul fmuls fmulsu icall ijmp in inc jmp ld ldd ldi lds lpm lsl lsr mov movw mul " .
				"muls mulsu neg nop or ori out pop push rcall ret reti rjmp rol ror sbc sbr sbrc sbrs " .
				"sec seh sbi sbci sbic sbis sbiw sei sen ser ses set sev sez sleep spm st std sts sub " .
				"subi swap tst wdr",
			"built_in" =>
				/* general purpose registers */
				"r0 r1 r2 r3 r4 r5 r6 r7 r8 r9 r10 r11 r12 r13 r14 r15 r16 r17 r18 r19 r20 r21 r22 " .
				"r23 r24 r25 r26 r27 r28 r29 r30 r31 x|0 xh xl y|0 yh yl z|0 zh zl " .
				/* IO Registers (ATMega128) */
				"ucsr1c udr1 ucsr1a ucsr1b ubrr1l ubrr1h ucsr0c ubrr0h tccr3c tccr3a tccr3b tcnt3h " .
				"tcnt3l ocr3ah ocr3al ocr3bh ocr3bl ocr3ch ocr3cl icr3h icr3l etimsk etifr tccr1c " .
				"ocr1ch ocr1cl twcr twdr twar twsr twbr osccal xmcra xmcrb eicra spmcsr spmcr portg " .
				"ddrg ping portf ddrf sreg sph spl xdiv rampz eicrb eimsk gimsk gicr eifr gifr timsk " .
				"tifr mcucr mcucsr tccr0 tcnt0 ocr0 assr tccr1a tccr1b tcnt1h tcnt1l ocr1ah ocr1al " .
				"ocr1bh ocr1bl icr1h icr1l tccr2 tcnt2 ocr2 ocdr wdtcr sfior eearh eearl eedr eecr " .
				"porta ddra pina portb ddrb pinb portc ddrc pinc portd ddrd pind spdr spsr spcr udr0 " .
				"ucsr0a ucsr0b ubrr0l acsr admux adcsr adch adcl porte ddre pine pinf"
		);
	}

	protected function getContainedModes() {
				
		return array(
			$this->C_BLOCK_COMMENT_MODE,
			new Mode(array(
				"className" => "comment", 
				"begin" => ";",  
				"end" => "$"
			)),
			$this->C_NUMBER_MODE, // 0x..., decimal, float
			$this->BINARY_NUMBER_MODE, // 0b...
			new Mode(array(
				"className" => "number",
				"begin" => "\b(\$[a-zA-Z0-9]+|0o[0-7]+)" // $..., 0o...
			)),
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "string",
				"begin" => "'", 
				"end" => "[^\\\]?'",
				"illegal" => "[^\\\][^']"
			)),
			new Mode(array(
				"className" => "label",  
				"begin" => "^[A-Za-z0-9_.$]+:"
			)),
			new Mode(array(
				"className" => "preprocessor", 
				"begin" => "#", 
				"end" => "$"
			)),
			new Mode(array(  // директивы «.include» «.macro» и т.д.
				"className" => "preprocessor",
				"begin" => "\.[a-zA-Z]+"
			)),
			new Mode(array(  // подстановка в «.macro»
				"className" => "localvars",
				"begin" => "@[0-9]+"
			))
		);
		
	}

}

?>
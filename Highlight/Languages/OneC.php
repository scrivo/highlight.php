<?php
/* Copyright (c)
 * - 2006-2013, Yuri Ivanov (ivanov@supersoft.ru), highlight.js
 *              (original author)
 * - 2006-2013, Sergey Baranov (segyrn@yandex.ru), highlight.js
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
 * This file is a direct port of 1c.js, the 1C language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class OneC extends Language {
	
	private $IDENT_RE_RU = "[a-zA-Zа-яА-Я][a-zA-Z0-9_а-яА-Я]*";
	
	protected function getName() {
		return "1c";
	}
	
	protected function getLexems() {
		return $this->IDENT_RE_RU;
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"возврат дата для если и или иначе иначеесли исключение конецесли " .
				"конецпопытки конецпроцедуры конецфункции конеццикла константа не перейти перем " .
				"перечисление по пока попытка прервать продолжить процедура строка тогда фс функция цикл " .
				"число экспорт",
			"built_in" =>
				"ansitooem oemtoansi ввестивидсубконто ввестидату ввестизначение " .
				"ввестиперечисление ввестипериод ввестиплансчетов ввестистроку ввестичисло вопрос " .
				"восстановитьзначение врег выбранныйплансчетов вызватьисключение датагод датамесяц " .
				"датачисло добавитьмесяц завершитьработусистемы заголовоксистемы записьжурналарегистрации " .
				"запуститьприложение зафиксироватьтранзакцию значениевстроку значениевстрокувнутр " .
				"значениевфайл значениеизстроки значениеизстрокивнутр значениеизфайла имякомпьютера " .
				"имяпользователя каталогвременныхфайлов каталогиб каталогпользователя каталогпрограммы " .
				"кодсимв командасистемы конгода конецпериодаби конецрассчитанногопериодаби " .
				"конецстандартногоинтервала конквартала конмесяца коннедели лев лог лог10 макс " .
				"максимальноеколичествосубконто мин монопольныйрежим названиеинтерфейса названиенабораправ " .
				"назначитьвид назначитьсчет найти найтипомеченныенаудаление найтиссылки началопериодаби " .
				"началостандартногоинтервала начатьтранзакцию начгода начквартала начмесяца начнедели " .
				"номерднягода номерднянедели номернеделигода нрег обработкаожидания окр описаниеошибки " .
				"основнойжурналрасчетов основнойплансчетов основнойязык открытьформу открытьформумодально " .
				"отменитьтранзакцию очиститьокносообщений периодстр полноеимяпользователя получитьвремята " .
				"получитьдатута получитьдокументта получитьзначенияотбора получитьпозициюта " .
				"получитьпустоезначение получитьта прав праводоступа предупреждение префиксавтонумерации " .
				"пустаястрока пустоезначение рабочаядаттьпустоезначение рабочаядата разделительстраниц " .
				"разделительстрок разм разобратьпозициюдокумента рассчитатьрегистрына " .
				"рассчитатьрегистрыпо сигнал симв символтабуляции создатьобъект сокрл сокрлп сокрп " .
				"сообщить состояние сохранитьзначение сред статусвозврата стрдлина стрзаменить " .
				"стрколичествострок стрполучитьстроку стрчисловхождений сформироватьпозициюдокумента " .
				"счетпокоду текущаядата текущеевремя типзначения типзначениястр удалитьобъекты " .
				"установитьтана установитьтапо фиксшаблон формат цел шаблон"
		);
	}

	protected function getContainedModes() {
		
		$DQUOTE =  new Mode(array(
			"className" => "dquote",  
			"begin" => "\"\""
		));
		
		$STR_START = new Mode(array(
			"className" => "string",
			"begin" => "\"", 
			"end" => "\"|$",
			"contains" => array($DQUOTE),
			"relevance" => 0
		));
		
		$STR_CONT = new Mode(array(
			"className" => "string",
			"begin" => "\|", 
			"end" => "\"|$",
			"contains" => array($DQUOTE)
		));
		
		return array(
			$this->C_LINE_COMMENT_MODE,
			$this->NUMBER_MODE,
			$STR_START, 
			$STR_CONT,
			new Mode(array(
				"className" => "function",
				"begin" => "(процедура|функция)", 
				"end" => "$",
				"lexems" => $this->IDENT_RE_RU,
				"keywords" => "процедура функция",
				"contains" => array(
					new Mode(array(
						"className" => "title", 
						"begin" => $this->IDENT_RE_RU
					)),
					new Mode(array(
						"className" => "tail",
						"endsWithParent" => true,
						"contains" => array(
							new Mode(array(
								"className" => "params",
								"begin" => "\(", 
								"end" => "\)",
								"lexems" => $this->IDENT_RE_RU,
								"keywords" => "знач",
								"contains" => array($STR_START, $STR_CONT)
							)),
							new Mode(array(
								"className" => "export",
								"begin" => "экспорт", 
								"endsWithParent" => true,
								"lexems" => $this->IDENT_RE_RU,
								"keywords" => "экспорт",
								"contains" => array($this->C_LINE_COMMENT_MODE)
							))
						)
					)),
					$this->C_LINE_COMMENT_MODE
				)
			)),
			new Mode(array(
				"className" => "preprocessor", 
				"begin" => "#", 
				"end" => "$"
			)),
			new Mode(array(
				"className" => "date", 
				"begin" => "'\d{2}\.\d{2}\.(\d{2}|\d{4})'"
			))
		);
		
	}

}

?>
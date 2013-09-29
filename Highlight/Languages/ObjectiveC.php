<?php
/* Copyright (c)
 * - 2006-2013, Valerii Hiora (valerii.hiora@gmail.com), highlight.js
 *              (original author)
 *              Angel G. Olloqui (angelgarcia.mail@gmail.com), highlight.js
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
 * This file is a direct port of objectivc.js, the Objective C language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class ObjectiveC extends Language {
	
	protected function getName() {
		return "objectivec";
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" =>
				"int float while private char catch export sizeof typedef const struct for union " .
				"unsigned long volatile static protected bool mutable if public do return goto void " .
				"enum else break extern asm case short default double throw register explicit " .
				"signed typename try this switch continue wchar_t inline readonly assign property " .
				"self synchronized end synthesize id optional required " .
				"nonatomic super unichar finally dynamic IBOutlet IBAction selector strong " .
				"weak readonly",
			"literal" =>
				"false true FALSE TRUE nil YES NO NULL",
			"built_in" =>
				"NSString NSDictionary CGRect CGPoint UIButton UILabel UITextView UIWebView MKMapView " .
				"UISegmentedControl NSObject UITableViewDelegate UITableViewDataSource NSThread " .
				"UIActivityIndicator UITabbar UIToolBar UIBarButtonItem UIImageView NSAutoreleasePool " .
				"UITableView BOOL NSInteger CGFloat NSException NSLog NSMutableString NSMutableArray " .
				"NSMutableDictionary NSURL NSIndexPath CGSize UITableViewCell UIView UIViewController " .
				"UINavigationBar UINavigationController UITabBarController UIPopoverController " .
				"UIPopoverControllerDelegate UIImage NSNumber UISearchBar NSFetchedResultsController " .
				"NSFetchedResultsChangeType UIScrollView UIScrollViewDelegate UIEdgeInsets UIColor " .
				"UIFont UIApplication NSNotFound NSNotificationCenter NSNotification " .
				"UILocalNotification NSBundle NSFileManager NSTimeInterval NSDate NSCalendar " .
				"NSUserDefaults UIWindow NSRange NSArray NSError NSURLRequest NSURLConnection " .
				"UIInterfaceOrientation MPMoviePlayerController dispatch_once_t " .
				"dispatch_queue_t dispatch_sync dispatch_async dispatch_once"				
		);
	}
	
	protected function getIllegal() {
		return "<\/";
	}

	protected function getContainedModes() {
	
		return array(
			$this->C_LINE_COMMENT_MODE,
			$this->C_BLOCK_COMMENT_MODE,
			$this->C_NUMBER_MODE,
			$this->QUOTE_STRING_MODE,
			new Mode(array(
				"className" => "string",
				"begin" => "'",
				"end" => "[^\\\\]'",
				"illegal" => "[^\\\\][^']"
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#import",
				"end" => "$",
				"contains" => [
				new Mode(array(
					"className" => "title",
					"begin" => "\"",
					"end" => "\""
				)),
				new Mode(array(
					"className" => "title",
					"begin" => "<",
					"end" => ">"
				))
				]
			)),
			new Mode(array(
				"className" => "preprocessor",
				"begin" => "#",
				"end" => "$"
			)),
			new Mode(array(
				"className" => "class",
				"beginWithKeyword" => true,
				"end" => "({|$)",
				"keywords" => "interface class protocol implementation",
				"contains" => [
					new Mode(array(
						"className" => "id",
						"begin" => $this->UNDERSCORE_IDENT_RE
					))
				]
			)),
			new Mode(array(
				"className" => "variable",
				"begin" => "\.".$this->UNDERSCORE_IDENT_RE,
				"relevance" => 0
			))
		);
	}

}

?>
<?php
/* Copyright (c)
 * - 2006-2013, Peter Leonov (gojpeg@yandex.ru), highlight.js (original author)
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
 * This file is a direct port of perl.js, the Perl language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Perl extends Language {
	
	protected function getName() {
		return "perl";
	}
	
	private function GET_KEYWORDS() {
		return
			"getpwent getservent quotemeta msgrcv scalar kill dbmclose undef lc " .
			"ma syswrite tr send umask sysopen shmwrite vec qx utime local oct semctl localtime " .
			"readpipe do return format read sprintf dbmopen pop getpgrp not getpwnam rewinddir qq" .
			"fileno qw endprotoent wait sethostent bless s|0 opendir continue each sleep endgrent " .
			"shutdown dump chomp connect getsockname die socketpair close flock exists index shmget" .
			"sub for endpwent redo lstat msgctl setpgrp abs exit select print ref gethostbyaddr " .
			"unshift fcntl syscall goto getnetbyaddr join gmtime symlink semget splice x|0 " .
			"getpeername recv log setsockopt cos last reverse gethostbyname getgrnam study formline " .
			"endhostent times chop length gethostent getnetent pack getprotoent getservbyname rand " .
			"mkdir pos chmod y|0 substr endnetent printf next open msgsnd readdir use unlink " .
			"getsockopt getpriority rindex wantarray hex system getservbyport endservent int chr " .
			"untie rmdir prototype tell listen fork shmread ucfirst setprotoent else sysseek link " .
			"getgrgid shmctl waitpid unpack getnetbyname reset chdir grep split require caller " .
			"lcfirst until warn while values shift telldir getpwuid my getprotobynumber delete and " .
			"sort uc defined srand accept package seekdir getprotobyname semop our rename seek if q|0 " .
			"chroot sysread setpwent no crypt getc chown sqrt write setnetent setpriority foreach " .
			"tie sin msgget map stat getlogin unless elsif truncate exec keys glob tied closedir" .
			"ioctl socket readlink eval xor readline binmode setservent eof ord bind alarm pipe " .
			"atan2 getgrent exp time push setgrent gt lt or ne m|0 break given say state when";
	}
	
	protected function getKeywords() {
		return $this->GET_KEYWORDS();
	}
		
	protected function getContainedModes() {
		
		$SUBST = new Mode(array(
			"className" => "subst",
			"begin" => "[$@]\{",
			"end" => "\}",
			"keywords" => $this->GET_KEYWORDS(),
			"relevance" => 10
		));
		
		$VAR1 = new Mode(array(
			"className" => "variable",
			"begin" => "\\$\d"
		));
		
		$VAR2 = new Mode(array(
			"className" => "variable",
			"begin" => "[\\$\%\@\*](\^\w\b|#\w+(\:\:\w+)*|[^\s\w{]|{\w+}|\w+(\:\:\w*)*)"
		));
		
		$STRING_CONTAINS = array($this->BACKSLASH_ESCAPE, $SUBST, $VAR1, $VAR2);
		
		$METHOD = new Mode(array(
			"begin" => "->",
			"contains" => array(
				new Mode(array(
					"begin" => $this->IDENT_RE
				)),
				new Mode(array(
					"begin" => "{",
					"end" => "}"
				))
			)
		));
		
		$COMMENT = new Mode(array(
			"className" => "comment",
			"begin" => "^(__END__|__DATA__)",
			"end" => "\n$",
			"relevance" => 5
		));
		
		$PERL_DEFAULT_CONTAINS = array(
			$VAR1, 
			$VAR2,
			$this->HASH_COMMENT_MODE,
			$COMMENT,
			new Mode(array(
				"className" => "comment",
				"begin" => "^\=\w",
				"end" => "\=cut", "endsWithParent" => true
			)),
			$METHOD,
			new Mode(array(
				"className" => "string",
				"begin" => "q[qwxr]?\s*\(",
				"end" => "\)",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "q[qwxr]?\s*\[",
				"end" => "\]",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "q[qwxr]?\s*\{",
				"end" => "\}",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "q[qwxr]?\s*\|",
				"end" => "\|",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "q[qwxr]?\s*\<",
				"end" => "\>",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "qw\s+q",
				"end" => "q",
				"contains" => $STRING_CONTAINS,
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\'",
				"end" => "\'",
				"contains" => array($this->BACKSLASH_ESCAPE),
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\"",
				"end" => "\"",
				"contains" => $STRING_CONTAINS,
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "`",
				"end" => "`",
				"contains" => array($this->BACKSLASH_ESCAPE)
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "{\w+}",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "string",
				"begin" => "\-?\w+\s*\=\>",
				"relevance" => 0
			)),
			new Mode(array(
				"className" => "number",
				"begin" => "(\b0[0-7_]+)|(\b0x[0-9a-fA-F_]+)|(\b[1-9][0-9_]*(\.[0-9_]+)?)|[0_]\b",
				"relevance" => 0
			)),
			new Mode(array( // regexp container
				"begin" => "(" . $this->RE_STARTERS_RE . "|\b(split|return|print|reverse|grep)\b)\s*",
				"keywords" => "split return print reverse grep",
				"relevance" => 0,
				"contains" => array(
					$this->HASH_COMMENT_MODE,
					$COMMENT,
					new Mode(array(
						"className" => "regexp",
						"begin" => "(s|tr|y)\/(\\.|[^\/])*\/(\\.|[^\/])*\/[a-z]*",
						"relevance" => 10
					)),
					new Mode(array(
						"className" => "regexp",
						"begin" => "(m|qr)?\/",
						"end" => "\/[a-z]*",
						"contains" => array($this->BACKSLASH_ESCAPE),
						"relevance" => 0 // allows empty "//" which is a common comment delimiter in other languages
					))
				)
			)),
			new Mode(array(
				"className" => "sub",
				"beginWithKeyword" => true,
				"end" => "(\s*\(.*?\))?[;{]",
				"keywords" => "sub",
				"relevance" => 5
			)),
			new Mode(array(
				"className" => "operator",
				"begin" => "-\w\b",
				"relevance" => 0
			))
		);
		
		$SUBST->contains = $PERL_DEFAULT_CONTAINS;
		$METHOD->contains[1]->contains = $PERL_DEFAULT_CONTAINS;
					
		return $PERL_DEFAULT_CONTAINS;
		
	}

}

?>
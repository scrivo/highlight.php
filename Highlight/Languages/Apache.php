<?php
/* Copyright (c)
 * - 2006-2013, Ruslan Keba (rukeba@gmail.com), highlight.js (original author)
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
 * This file is a direct port of apache.js, the Apache language definition 
 * file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class Apache extends Language {
	
	protected function getName() {
		return "apache";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return array(
			"keyword" => 
				"acceptfilter acceptmutex acceptpathinfo accessfilename action addalt " .
				"addaltbyencoding addaltbytype addcharset adddefaultcharset adddescription " .
				"addencoding addhandler addicon addiconbyencoding addiconbytype addinputfilter " .
				"addlanguage addmoduleinfo addoutputfilter addoutputfilterbytype addtype alias " .
				"aliasmatch allow allowconnect allowencodedslashes allowoverride anonymous " .
				"anonymous_logemail anonymous_mustgiveemail anonymous_nouserid anonymous_verifyemail " .
				"authbasicauthoritative authbasicprovider authdbduserpwquery authdbduserrealmquery " .
				"authdbmgroupfile authdbmtype authdbmuserfile authdefaultauthoritative " .
				"authdigestalgorithm authdigestdomain authdigestnccheck authdigestnonceformat " .
				"authdigestnoncelifetime authdigestprovider authdigestqop authdigestshmemsize " .
				"authgroupfile authldapbinddn authldapbindpassword authldapcharsetconfig " .
				"authldapcomparednonserver authldapdereferencealiases authldapgroupattribute " .
				"authldapgroupattributeisdn authldapremoteuserattribute authldapremoteuserisdn " .
				"authldapurl authname authnprovideralias authtype authuserfile authzdbmauthoritative " .
				"authzdbmtype authzdefaultauthoritative authzgroupfileauthoritative " .
				"authzldapauthoritative authzownerauthoritative authzuserauthoritative " .
				"balancermember browsermatch browsermatchnocase bufferedlogs cachedefaultexpire " .
				"cachedirlength cachedirlevels cachedisable cacheenable cachefile " .
				"cacheignorecachecontrol cacheignoreheaders cacheignorenolastmod " .
				"cacheignorequerystring cachelastmodifiedfactor cachemaxexpire cachemaxfilesize " .
				"cacheminfilesize cachenegotiateddocs cacheroot cachestorenostore cachestoreprivate " .
				"cgimapextension charsetdefault charsetoptions charsetsourceenc checkcaseonly " .
				"checkspelling chrootdir contentdigest cookiedomain cookieexpires cookielog " .
				"cookiename cookiestyle cookietracking coredumpdirectory customlog dav " .
				"davdepthinfinity davgenericlockdb davlockdb davmintimeout dbdexptime dbdkeep " .
				"dbdmax dbdmin dbdparams dbdpersist dbdpreparesql dbdriver defaulticon " .
				"defaultlanguage defaulttype deflatebuffersize deflatecompressionlevel " .
				"deflatefilternote deflatememlevel deflatewindowsize deny directoryindex " .
				"directorymatch directoryslash documentroot dumpioinput dumpiologlevel dumpiooutput " .
				"enableexceptionhook enablemmap enablesendfile errordocument errorlog example " .
				"expiresactive expiresbytype expiresdefault extendedstatus extfilterdefine " .
				"extfilteroptions fileetag filterchain filterdeclare filterprotocol filterprovider " .
				"filtertrace forcelanguagepriority forcetype forensiclog gracefulshutdowntimeout " .
				"group header headername hostnamelookups identitycheck identitychecktimeout " .
				"imapbase imapdefault imapmenu include indexheadinsert indexignore indexoptions " .
				"indexorderdefault indexstylesheet isapiappendlogtoerrors isapiappendlogtoquery " .
				"isapicachefile isapifakeasync isapilognotsupported isapireadaheadbuffer keepalive " .
				"keepalivetimeout languagepriority ldapcacheentries ldapcachettl " .
				"ldapconnectiontimeout ldapopcacheentries ldapopcachettl ldapsharedcachefile " .
				"ldapsharedcachesize ldaptrustedclientcert ldaptrustedglobalcert ldaptrustedmode " .
				"ldapverifyservercert limitinternalrecursion limitrequestbody limitrequestfields " .
				"limitrequestfieldsize limitrequestline limitxmlrequestbody listen listenbacklog " .
				"loadfile loadmodule lockfile logformat loglevel maxclients maxkeepaliverequests " .
				"maxmemfree maxrequestsperchild maxrequestsperthread maxspareservers maxsparethreads " .
				"maxthreads mcachemaxobjectcount mcachemaxobjectsize mcachemaxstreamingbuffer " .
				"mcacheminobjectsize mcacheremovalalgorithm mcachesize metadir metafiles metasuffix " .
				"mimemagicfile minspareservers minsparethreads mmapfile mod_gzip_on " .
				"mod_gzip_add_header_count mod_gzip_keep_workfiles mod_gzip_dechunk " .
				"mod_gzip_min_http mod_gzip_minimum_file_size mod_gzip_maximum_file_size " .
				"mod_gzip_maximum_inmem_size mod_gzip_temp_dir mod_gzip_item_include " .
				"mod_gzip_item_exclude mod_gzip_command_version mod_gzip_can_negotiate " .
				"mod_gzip_handle_methods mod_gzip_static_suffix mod_gzip_send_vary " .
				"mod_gzip_update_static modmimeusepathinfo multiviewsmatch namevirtualhost noproxy " .
				"nwssltrustedcerts nwsslupgradeable options order passenv pidfile protocolecho " .
				"proxybadheader proxyblock proxydomain proxyerroroverride proxyftpdircharset " .
				"proxyiobuffersize proxymaxforwards proxypass proxypassinterpolateenv " .
				"proxypassmatch proxypassreverse proxypassreversecookiedomain " .
				"proxypassreversecookiepath proxypreservehost proxyreceivebuffersize proxyremote " .
				"proxyremotematch proxyrequests proxyset proxystatus proxytimeout proxyvia " .
				"readmename receivebuffersize redirect redirectmatch redirectpermanent " .
				"redirecttemp removecharset removeencoding removehandler removeinputfilter " .
				"removelanguage removeoutputfilter removetype requestheader require rewritebase " .
				"rewritecond rewriteengine rewritelock rewritelog rewriteloglevel rewritemap " .
				"rewriteoptions rewriterule rlimitcpu rlimitmem rlimitnproc satisfy scoreboardfile " .
				"script scriptalias scriptaliasmatch scriptinterpretersource scriptlog " .
				"scriptlogbuffer scriptloglength scriptsock securelisten seerequesttail " .
				"sendbuffersize serveradmin serveralias serverlimit servername serverpath " .
				"serverroot serversignature servertokens setenv setenvif setenvifnocase sethandler " .
				"setinputfilter setoutputfilter ssienableaccess ssiendtag ssierrormsg ssistarttag " .
				"ssitimeformat ssiundefinedecho sslcacertificatefile sslcacertificatepath " .
				"sslcadnrequestfile sslcadnrequestpath sslcarevocationfile sslcarevocationpath " .
				"sslcertificatechainfile sslcertificatefile sslcertificatekeyfile sslciphersuite " .
				"sslcryptodevice sslengine sslhonorciperorder sslmutex ssloptions " .
				"sslpassphrasedialog sslprotocol sslproxycacertificatefile " .
				"sslproxycacertificatepath sslproxycarevocationfile sslproxycarevocationpath " .
				"sslproxyciphersuite sslproxyengine sslproxymachinecertificatefile " .
				"sslproxymachinecertificatepath sslproxyprotocol sslproxyverify " .
				"sslproxyverifydepth sslrandomseed sslrequire sslrequiressl sslsessioncache " .
				"sslsessioncachetimeout sslusername sslverifyclient sslverifydepth startservers " .
				"startthreads substitute suexecusergroup threadlimit threadsperchild " .
				"threadstacksize timeout traceenable transferlog typesconfig unsetenv " .
				"usecanonicalname usecanonicalphysicalport user userdir virtualdocumentroot " .
				"virtualdocumentrootip virtualscriptalias virtualscriptaliasip " .
				"win32disableacceptex xbithack",
			"literal" =>
				 "on off"
		);
	}
	
	protected function getContainedModes() {
		
		$NUMBER = new Mode(array(
			"className" => "number", 
			"begin" => "[\$%]\d+"
		));
		
		return array(
			$this->HASH_COMMENT_MODE,
			new Mode(array(
				"className" => "sqbracket",
				"begin" => "\s\[", 
				"end" => "\]$"
			)),
			new Mode(array(
				"className" => "cbracket",
				"begin" => "[\$%]\{", 
				"end" => "\}",
				"contains" => array("self", $NUMBER)
			)),
			$NUMBER,
			new Mode(array(
				"className" => "tag", 
				"begin" => "<\/?", 
				"end" => ">"
			)),
			$this->QUOTE_STRING_MODE
		);
		
	}

}

?>
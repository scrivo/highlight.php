<?php

function pf($hljs)
{
    $MACRO = (object) array('className' => 'variable', 'begin' => new Highlight\RegEx('/\\\\\\$\\[\\\\w\\\\d\\#@\\]\\[\\\\w\\\\d_\\]\\*/'), 'relevance' => 0);
    $TABLE = (object) array('className' => 'variable', 'begin' => new Highlight\RegEx('/\\<\\(\\?\\!\\\\\\/\\)/'), 'end' => new Highlight\RegEx('/\\>/'));

    return (object) array('name' => 'Packet Filter config', 'aliases' => array('pf.conf'), 'keywords' => (object) array('$pattern' => new Highlight\RegEx('/\\[a\\-z0\\-9_\\<\\>\\-\\]\\+/'), 'built_in' => 'block match pass load anchor|5 antispoof|10 set table', 'keyword' => 'in out log quick on rdomain inet inet6 proto from port os to route allow-opts divert-packet divert-reply divert-to flags group icmp-type icmp6-type label once probability recieved-on rtable prio queue tos tag tagged user keep fragment for os drop af-to|10 binat-to|10 nat-to|10 rdr-to|10 bitmask least-stats random round-robin source-hash static-port dup-to reply-to route-to parent bandwidth default min max qlimit block-policy debug fingerprints hostid limit loginterface optimization reassemble ruleset-optimization basic none profile skip state-defaults state-policy timeout const counters persist no modulate synproxy state|5 floating if-bound no-sync pflow|10 sloppy source-track global rule max-src-nodes max-src-states max-src-conn max-src-conn-rate overload flush scrub|5 max-mss min-ttl no-df|10 random-id', 'literal' => 'all any no-route self urpf-failed egress|5 unknown'), 'contains' => array($hljs->HASH_COMMENT_MODE, $hljs->NUMBER_MODE, $hljs->QUOTE_STRING_MODE, $MACRO, $TABLE));
}
$module->exports = $pf;

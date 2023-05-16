<?php

function haxe($hljs)
{
    $HAXE_BASIC_TYPES = 'Int Float String Bool Dynamic Void Array ';

    return (object) array('name' => 'Haxe', 'aliases' => array('hx'), 'keywords' => (object) array('keyword' => 'break case cast catch continue default do dynamic else enum extern for function here if import in inline never new override package private get set public return static super switch this throw trace try typedef untyped using var while ' . $HAXE_BASIC_TYPES, 'built_in' => 'trace this', 'literal' => 'true false null _'), 'contains' => array((object) array('className' => 'string', 'begin' => '\'', 'end' => '\'', 'contains' => array($hljs->BACKSLASH_ESCAPE, (object) array('className' => 'subst', 'begin' => '\\$\\{', 'end' => '\\}'), (object) array('className' => 'subst', 'begin' => '\\$', 'end' => new Highlight\RegEx('/\\\\W\\\\\\}/')))), $hljs->QUOTE_STRING_MODE, $hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $hljs->C_NUMBER_MODE, (object) array('className' => 'meta', 'begin' => '@:', 'end' => '$'), (object) array('className' => 'meta', 'begin' => '#', 'end' => '$', 'keywords' => (object) array('keyword' => 'if else elseif end error')), (object) array('className' => 'type', 'begin' => ':[ 	]*', 'end' => '[^A-Za-z0-9_ 	\\->]', 'excludeBegin' => true, 'excludeEnd' => true, 'relevance' => 0), (object) array('className' => 'type', 'begin' => ':[ 	]*', 'end' => '\\W', 'excludeBegin' => true, 'excludeEnd' => true), (object) array('className' => 'type', 'begin' => 'new *', 'end' => '\\W', 'excludeBegin' => true, 'excludeEnd' => true), (object) array('className' => 'class', 'beginKeywords' => 'enum', 'end' => '\\{', 'contains' => array($hljs->TITLE_MODE)), (object) array('className' => 'class', 'beginKeywords' => 'abstract', 'end' => '[\\{$]', 'contains' => array((object) array('className' => 'type', 'begin' => '\\(', 'end' => '\\)', 'excludeBegin' => true, 'excludeEnd' => true), (object) array('className' => 'type', 'begin' => 'from +', 'end' => '\\W', 'excludeBegin' => true, 'excludeEnd' => true), (object) array('className' => 'type', 'begin' => 'to +', 'end' => '\\W', 'excludeBegin' => true, 'excludeEnd' => true), $hljs->TITLE_MODE), 'keywords' => (object) array('keyword' => 'abstract from to')), (object) array('className' => 'class', 'begin' => '\\b(class|interface) +', 'end' => '[\\{$]', 'excludeEnd' => true, 'keywords' => 'class interface', 'contains' => array((object) array('className' => 'keyword', 'begin' => '\\b(extends|implements) +', 'keywords' => 'extends implements', 'contains' => array((object) array('className' => 'type', 'begin' => $hljs->IDENT_RE, 'relevance' => 0))), $hljs->TITLE_MODE)), (object) array('className' => 'function', 'beginKeywords' => 'function', 'end' => '\\(', 'excludeEnd' => true, 'illegal' => '\\S', 'contains' => array($hljs->TITLE_MODE))), 'illegal' => new Highlight\RegEx('/\\<\\\\\\//'));
}
$module->exports = $haxe;

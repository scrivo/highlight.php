<?php

function nix($hljs)
{
    $KEYWORDS = (object) array('keyword' => array('rec', 'with', 'let', 'in', 'inherit', 'assert', 'if', 'else', 'then'), 'literal' => array('true', 'false', 'or', 'and', 'null'), 'built_in' => array('import', 'abort', 'baseNameOf', 'dirOf', 'isNull', 'builtins', 'map', 'removeAttrs', 'throw', 'toString', 'derivation'));
    $ANTIQUOTE = (object) array('className' => 'subst', 'begin' => new Highlight\RegEx('/\\\\\\$\\\\\\{/'), 'end' => new Highlight\RegEx('/\\\\\\}/'), 'keywords' => $KEYWORDS);
    $ESCAPED_DOLLAR = (object) array('className' => 'char.escape', 'begin' => new Highlight\RegEx('/\'\'\\\\\\$/'));
    $ATTRS = (object) array('begin' => new Highlight\RegEx('/\\[a\\-zA\\-Z0\\-9\\-_\\]\\+\\(\\\\s\\*\\=\\)/'), 'returnBegin' => true, 'relevance' => 0, 'contains' => array((object) array('className' => 'attr', 'begin' => new Highlight\RegEx('/\\\\S\\+/'), 'relevance' => 0.2)));
    $STRING = (object) array('className' => 'string', 'contains' => array($ESCAPED_DOLLAR, $ANTIQUOTE), 'variants' => array((object) array('begin' => '\'\'', 'end' => '\'\''), (object) array('begin' => '"', 'end' => '"')));
    $EXPRESSIONS = array($hljs->NUMBER_MODE, $hljs->HASH_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $STRING, $ATTRS);
    $ANTIQUOTE->contains = $EXPRESSIONS;

    return (object) array('name' => 'Nix', 'aliases' => array('nixos'), 'keywords' => $KEYWORDS, 'contains' => $EXPRESSIONS);
}
$module->exports = $nix;

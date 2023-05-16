<?php

function ada($hljs)
{
    $INTEGER_RE = '\\d(_|\\d)*';
    $EXPONENT_RE = '[eE][-+]?' + $INTEGER_RE;
    $DECIMAL_LITERAL_RE = $INTEGER_RE + '(\\.' + $INTEGER_RE + ')?' + '(' + $EXPONENT_RE + ')?';
    $BASED_INTEGER_RE = '\\w+';
    $BASED_LITERAL_RE = $INTEGER_RE + '#' + $BASED_INTEGER_RE + '(\\.' + $BASED_INTEGER_RE + ')?' + '#' + '(' + $EXPONENT_RE + ')?';
    $NUMBER_RE = '\\b(' + $BASED_LITERAL_RE + '|' + $DECIMAL_LITERAL_RE + ')';
    $ID_REGEX = '[A-Za-z](_?[A-Za-z0-9.])*';
    $BAD_CHARS = '[]\\{\\}%#\'"';
    $COMMENTS = $hljs->COMMENT('--', '$');
    $VAR_DECLS = (object) array('begin' => '\\s+:\\s+', 'end' => '\\s*(:=|;|\\)|=>|$)', 'illegal' => $BAD_CHARS, 'contains' => array((object) array('beginKeywords' => 'loop for declare others', 'endsParent' => true), (object) array('className' => 'keyword', 'beginKeywords' => 'not null constant access function procedure in out aliased exception'), (object) array('className' => 'type', 'begin' => $ID_REGEX, 'endsParent' => true, 'relevance' => 0)));
    $KEYWORDS = array('abort', 'else', 'new', 'return', 'abs', 'elsif', 'not', 'reverse', 'abstract', 'end', 'accept', 'entry', 'select', 'access', 'exception', 'of', 'separate', 'aliased', 'exit', 'or', 'some', 'all', 'others', 'subtype', 'and', 'for', 'out', 'synchronized', 'array', 'function', 'overriding', 'at', 'tagged', 'generic', 'package', 'task', 'begin', 'goto', 'pragma', 'terminate', 'body', 'private', 'then', 'if', 'procedure', 'type', 'case', 'in', 'protected', 'constant', 'interface', 'is', 'raise', 'use', 'declare', 'range', 'delay', 'limited', 'record', 'when', 'delta', 'loop', 'rem', 'while', 'digits', 'renames', 'with', 'do', 'mod', 'requeue', 'xor');

    return (object) array('name' => 'Ada', 'case_insensitive' => true, 'keywords' => (object) array('keyword' => $KEYWORDS, 'literal' => array('True', 'False')), 'contains' => array($COMMENTS, (object) array('className' => 'string', 'begin' => new Highlight\RegEx('/"/'), 'end' => new Highlight\RegEx('/"/'), 'contains' => array((object) array('begin' => new Highlight\RegEx('/""/'), 'relevance' => 0))), (object) array('className' => 'string', 'begin' => new Highlight\RegEx('/\'\\.\'/')), (object) array('className' => 'number', 'begin' => $NUMBER_RE, 'relevance' => 0), (object) array('className' => 'symbol', 'begin' => '\'' + $ID_REGEX), (object) array('className' => 'title', 'begin' => '(\\bwith\\s+)?(\\bprivate\\s+)?\\bpackage\\s+(\\bbody\\s+)?', 'end' => '(is|$)', 'keywords' => 'package body', 'excludeBegin' => true, 'excludeEnd' => true, 'illegal' => $BAD_CHARS), (object) array('begin' => '(\\b(with|overriding)\\s+)?\\b(function|procedure)\\s+', 'end' => '(\\bis|\\bwith|\\brenames|\\)\\s*;)', 'keywords' => 'overriding function procedure with is renames return', 'returnBegin' => true, 'contains' => array($COMMENTS, (object) array('className' => 'title', 'begin' => '(\\bwith\\s+)?\\b(function|procedure)\\s+', 'end' => '(\\(|\\s+|$)', 'excludeBegin' => true, 'excludeEnd' => true, 'illegal' => $BAD_CHARS), $VAR_DECLS, (object) array('className' => 'type', 'begin' => '\\breturn\\s+', 'end' => '(\\s+|;|$)', 'keywords' => 'return', 'excludeBegin' => true, 'excludeEnd' => true, 'endsParent' => true, 'illegal' => $BAD_CHARS))), (object) array('className' => 'type', 'begin' => '\\b(sub)?type\\s+', 'end' => '\\s+', 'keywords' => 'type', 'excludeBegin' => true, 'illegal' => $BAD_CHARS), $VAR_DECLS));
}
$module->exports = $ada;

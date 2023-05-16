<?php

function step21($hljs)
{
    $STEP21_IDENT_RE = '[A-Z_][A-Z0-9_.]*';
    $STEP21_KEYWORDS = (object) array('$pattern' => $STEP21_IDENT_RE, 'keyword' => array('HEADER', 'ENDSEC', 'DATA'));
    $STEP21_START = (object) array('className' => 'meta', 'begin' => 'ISO-10303-21;', 'relevance' => 10);
    $STEP21_CLOSE = (object) array('className' => 'meta', 'begin' => 'END-ISO-10303-21;', 'relevance' => 10);

    return (object) array('name' => 'STEP Part 21', 'aliases' => array('p21', 'step', 'stp'), 'case_insensitive' => true, 'keywords' => $STEP21_KEYWORDS, 'contains' => array($STEP21_START, $STEP21_CLOSE, $hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $hljs->COMMENT('/\\*\\*!', '\\*/'), $hljs->C_NUMBER_MODE, $hljs->inherit($hljs->APOS_STRING_MODE, (object) array('illegal' => null)), $hljs->inherit($hljs->QUOTE_STRING_MODE, (object) array('illegal' => null)), (object) array('className' => 'string', 'begin' => '\'', 'end' => '\''), (object) array('className' => 'symbol', 'variants' => array((object) array('begin' => '#', 'end' => '\\d+', 'illegal' => '\\W')))));
}
$module->exports = $step21;

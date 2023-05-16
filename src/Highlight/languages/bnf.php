<?php

function bnf($hljs)
{
    return (object) array('name' => 'Backus–Naur Form', 'contains' => array((object) array('className' => 'attribute', 'begin' => new Highlight\RegEx('/\\</'), 'end' => new Highlight\RegEx('/\\>/')), (object) array('begin' => new Highlight\RegEx('/\\:\\:\\=/'), 'end' => new Highlight\RegEx('/\\$/'), 'contains' => array((object) array('begin' => new Highlight\RegEx('/\\</'), 'end' => new Highlight\RegEx('/\\>/')), $hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $hljs->APOS_STRING_MODE, $hljs->QUOTE_STRING_MODE))));
}
$module->exports = $bnf;

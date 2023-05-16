<?php

function dts($hljs)
{
    $STRINGS = (object) array('className' => 'string', 'variants' => array($hljs->inherit($hljs->QUOTE_STRING_MODE, (object) array('begin' => '((u8?|U)|L)?"')), (object) array('begin' => '(u8?|U)?R"', 'end' => '"', 'contains' => array($hljs->BACKSLASH_ESCAPE)), (object) array('begin' => '\'\\\\?.', 'end' => '\'', 'illegal' => '.')));
    $NUMBERS = (object) array('className' => 'number', 'variants' => array((object) array('begin' => '\\b(\\d+(\\.\\d*)?|\\.\\d+)(u|U|l|L|ul|UL|f|F)'), (object) array('begin' => $hljs->C_NUMBER_RE)), 'relevance' => 0);
    $PREPROCESSOR = (object) array('className' => 'meta', 'begin' => '#', 'end' => '$', 'keywords' => (object) array('keyword' => 'if else elif endif define undef ifdef ifndef'), 'contains' => array((object) array('begin' => new Highlight\RegEx('/\\\\\\\\\\\\n/'), 'relevance' => 0), (object) array('beginKeywords' => 'include', 'end' => '$', 'keywords' => (object) array('keyword' => 'include'), 'contains' => array($hljs->inherit($STRINGS, (object) array('className' => 'string')), (object) array('className' => 'string', 'begin' => '<', 'end' => '>', 'illegal' => '\\n'))), $STRINGS, $hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE));
    $REFERENCE = (object) array('className' => 'variable', 'begin' => new Highlight\RegEx('/&\\[a\\-z\\\\d_\\]\\*\\\\b/'));
    $KEYWORD = (object) array('className' => 'keyword', 'begin' => '/[a-z][a-z\\d-]*/');
    $LABEL = (object) array('className' => 'symbol', 'begin' => '^\\s*[a-zA-Z_][a-zA-Z\\d_]*:');
    $CELL_PROPERTY = (object) array('className' => 'params', 'relevance' => 0, 'begin' => '<', 'end' => '>', 'contains' => array($NUMBERS, $REFERENCE));
    $NODE = (object) array('className' => 'title.class', 'begin' => new Highlight\RegEx('/\\[a\\-zA\\-Z_\\]\\[a\\-zA\\-Z\\\\d_@\\-\\]\\*\\(\\?\\=\\\\s\\\\\\{\\)/'), 'relevance' => 0.2);
    $ROOT_NODE = (object) array('className' => 'title.class', 'begin' => new Highlight\RegEx('/\\^\\\\\\/\\(\\?\\=\\\\s\\*\\\\\\{\\)/'), 'relevance' => 10);
    $ATTR_NO_VALUE = (object) array('match' => new Highlight\RegEx('/\\[a\\-z\\]\\[a\\-z\\-,\\]\\+\\(\\?\\=;\\)/'), 'relevance' => 0, 'scope' => 'attr');
    $ATTR = (object) array('relevance' => 0, 'match' => array(new Highlight\RegEx('/\\[a\\-z\\]\\[a\\-z\\-,\\]\\+/'), new Highlight\RegEx('/\\\\s\\*/'), new Highlight\RegEx('/\\=/')), 'scope' => (object) array());
    $PUNC = (object) array('scope' => 'punctuation', 'relevance' => 0, 'match' => new Highlight\RegEx('/\\\\\\};\\|\\[;\\{\\}\\]/'));

    return (object) array('name' => 'Device Tree', 'contains' => array($ROOT_NODE, $REFERENCE, $KEYWORD, $LABEL, $NODE, $ATTR, $ATTR_NO_VALUE, $CELL_PROPERTY, $hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $NUMBERS, $STRINGS, $PREPROCESSOR, $PUNC, (object) array('begin' => $hljs->IDENT_RE + '::', 'keywords' => '')));
}
$module->exports = $dts;

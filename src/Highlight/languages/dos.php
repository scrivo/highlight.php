<?php

function dos($hljs)
{
    $COMMENT = $hljs->COMMENT(new Highlight\RegEx('/\\^\\\\s\\*@\\?rem\\\\b/'), new Highlight\RegEx('/\\$/'), (object) array('relevance' => 10));
    $LABEL = (object) array('className' => 'symbol', 'begin' => '^\\s*[A-Za-z._?][A-Za-z0-9_$#@~.?]*(:|\\s+label)', 'relevance' => 0);
    $KEYWORDS = array('if', 'else', 'goto', 'for', 'in', 'do', 'call', 'exit', 'not', 'exist', 'errorlevel', 'defined', 'equ', 'neq', 'lss', 'leq', 'gtr', 'geq');
    $BUILT_INS = array('prn', 'nul', 'lpt3', 'lpt2', 'lpt1', 'con', 'com4', 'com3', 'com2', 'com1', 'aux', 'shift', 'cd', 'dir', 'echo', 'setlocal', 'endlocal', 'set', 'pause', 'copy', 'append', 'assoc', 'at', 'attrib', 'break', 'cacls', 'cd', 'chcp', 'chdir', 'chkdsk', 'chkntfs', 'cls', 'cmd', 'color', 'comp', 'compact', 'convert', 'date', 'dir', 'diskcomp', 'diskcopy', 'doskey', 'erase', 'fs', 'find', 'findstr', 'format', 'ftype', 'graftabl', 'help', 'keyb', 'label', 'md', 'mkdir', 'mode', 'more', 'move', 'path', 'pause', 'print', 'popd', 'pushd', 'promt', 'rd', 'recover', 'rem', 'rename', 'replace', 'restore', 'rmdir', 'shift', 'sort', 'start', 'subst', 'time', 'title', 'tree', 'type', 'ver', 'verify', 'vol', 'ping', 'net', 'ipconfig', 'taskkill', 'xcopy', 'ren', 'del');

    return (object) array('name' => 'Batch file (DOS)', 'aliases' => array('bat', 'cmd'), 'case_insensitive' => true, 'illegal' => new Highlight\RegEx('/\\\\\\/\\\\\\*/'), 'keywords' => (object) array('keyword' => $KEYWORDS, 'built_in' => $BUILT_INS), 'contains' => array((object) array('className' => 'variable', 'begin' => new Highlight\RegEx('/%%\\[\\^ \\]\\|%\\[\\^ \\]\\+\\?%\\|\\!\\[\\^ \\]\\+\\?\\!/')), (object) array('className' => 'function', 'begin' => $LABEL->begin, 'end' => 'goto:eof', 'contains' => array($hljs->inherit($hljs->TITLE_MODE, (object) array('begin' => '([_a-zA-Z]\\w*\\.)*([_a-zA-Z]\\w*:)?[_a-zA-Z]\\w*')), $COMMENT)), (object) array('className' => 'number', 'begin' => '\\b\\d+', 'relevance' => 0), $COMMENT));
}
$module->exports = $dos;

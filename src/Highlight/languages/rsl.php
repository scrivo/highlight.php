<?php

function rsl($hljs)
{
    $BUILT_INS = array('abs', 'acos', 'ambient', 'area', 'asin', 'atan', 'atmosphere', 'attribute', 'calculatenormal', 'ceil', 'cellnoise', 'clamp', 'comp', 'concat', 'cos', 'degrees', 'depth', 'Deriv', 'diffuse', 'distance', 'Du', 'Dv', 'environment', 'exp', 'faceforward', 'filterstep', 'floor', 'format', 'fresnel', 'incident', 'length', 'lightsource', 'log', 'match', 'max', 'min', 'mod', 'noise', 'normalize', 'ntransform', 'opposite', 'option', 'phong', 'pnoise', 'pow', 'printf', 'ptlined', 'radians', 'random', 'reflect', 'refract', 'renderinfo', 'round', 'setcomp', 'setxcomp', 'setycomp', 'setzcomp', 'shadow', 'sign', 'sin', 'smoothstep', 'specular', 'specularbrdf', 'spline', 'sqrt', 'step', 'tan', 'texture', 'textureinfo', 'trace', 'transform', 'vtransform', 'xcomp', 'ycomp', 'zcomp');
    $TYPES = array('matrix', 'float', 'color', 'point', 'normal', 'vector');
    $KEYWORDS = array('while', 'for', 'if', 'do', 'return', 'else', 'break', 'extern', 'continue');
    $CLASS_DEFINITION = (object) array('match' => array(new Highlight\RegEx('/\\(surface\\|displacement\\|light\\|volume\\|imager\\)/'), new Highlight\RegEx('/\\\\s\\+/'), $hljs->IDENT_RE), 'scope' => (object) array());

    return (object) array('name' => 'RenderMan RSL', 'keywords' => (object) array('keyword' => $KEYWORDS, 'built_in' => $BUILT_INS, 'type' => $TYPES), 'illegal' => '</', 'contains' => array($hljs->C_LINE_COMMENT_MODE, $hljs->C_BLOCK_COMMENT_MODE, $hljs->QUOTE_STRING_MODE, $hljs->APOS_STRING_MODE, $hljs->C_NUMBER_MODE, (object) array('className' => 'meta', 'begin' => '#', 'end' => '$'), $CLASS_DEFINITION, (object) array('beginKeywords' => 'illuminate illuminance gather', 'end' => '\\(')));
}
$module->exports = $rsl;

<?php

use allejo\Rosetta\Transformer\Transformer;
use Highlight\Translator\Transformer\RegExpLiteral;

$transformer = new Transformer();
$transformer->registerTransformer(RegExpLiteral::class);

return $transformer;

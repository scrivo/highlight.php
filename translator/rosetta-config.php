<?php

include __DIR__ . '/vendor/autoload.php';

use allejo\Rosetta\Transformer\Transformer;
use Highlight\Translator\Transformer\Constructs\RegExpLiteral;

$transformer = new Transformer();
$transformer->registerTransformer(RegExpLiteral::class);

return $transformer;

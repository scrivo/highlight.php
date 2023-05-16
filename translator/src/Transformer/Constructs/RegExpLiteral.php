<?php

namespace Highlight\Translator\Transformer\Constructs;

use allejo\Rosetta\Babel\RegExpLiteral as BabelRegExpLiteral;
use allejo\Rosetta\Transformer\Constructs\PhpConstructInterface;
use allejo\Rosetta\Transformer\Transformer;
use Highlight\RegEx;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;

/**
 * @implements PhpConstructInterface<BabelRegExpLiteral, New_>
 */
class RegExpLiteral implements PhpConstructInterface
{
    public static function getConstructName(): string
    {
        return 'RegExpLiteral';
    }

    /**
     * @param BabelRegExpLiteral $babelConstruct
     */
    public static function fromBabel($babelConstruct, Transformer $transformer): New_
    {
        $reSrc = sprintf('/%s/%s', preg_quote($babelConstruct->pattern, '/'), $babelConstruct->flags);

        return new New_(new Name(RegEx::class), array(new String_($reSrc)));
    }
}

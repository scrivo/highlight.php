<?php


namespace Highlight;

/**
 * @internal
 */
trait ModeDeprecations
{
    /**
     * @deprecated 9.16.0.0 Use `case_insensitive` instead
     * @var bool DEPRECATED
     */
    public $caseInsensitive;

    /**
     * @deprecated 9.16.0.0 Use `terminator_end` instead
     * @var string
     */
    public $terminatorEnd;
}

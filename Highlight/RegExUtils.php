<?php

namespace Highlight;

abstract class RegExUtils
{
    public static function langRe($value, $global, $case_insensitive)
    {
        // PCRE allows us to change the definition of "new line." The
        // `(*ANYCRLF)` matches `\r`, `\n`, and `\r\n` for `$`
        //
        //   https://www.pcre.org/original/doc/html/pcrepattern.html

        // PCRE requires us to tell it the string can be UTF-8, so the 'u' modifier
        // is required. The `u` flag for PCRE is different from JS' unicode flag.

        $escaped = preg_replace('#(?<!\\\)/#um', '\\/', $value);
        $regex = "/(*ANYCRLF){$escaped}/um" . ($case_insensitive ? "i" : "");

        return new RegEx($regex);
    }
}

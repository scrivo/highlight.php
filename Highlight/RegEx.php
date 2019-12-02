<?php

namespace Highlight;

/**
 * A PHP implementation to match JavaScript's RegExp class as closely as possible.
 *
 * A lot of behavior in this class is reversed engineered, so improvements are welcome!
 *
 * @internal
 *
 * @since 9.16.0
 */
final class RegEx extends ArbitraryProperties
{
    /**
     * @var string
     */
    public $source;

    /**
     * @var int
     */
    public $lastIndex = 0;

    /**
     * @param RegEx|string $regex
     */
    public function __construct($regex)
    {
        $this->source = (string) $regex;
    }

    public function __toString()
    {
        return (string) $this->source;
    }

    /**
     * Run the regular expression against the given string.
     *
     * @since 9.16.0.0
     *
     * @param string $str the string to run this regular expression against
     *
     * @return SafeProperties|null
     */
    public function exec($str)
    {
        $index = null;
        $results = array();
        preg_match_all($this->source, $str, $results, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, $this->lastIndex);

        if ($results === null || count($results) === 0) {
            return null;
        }

        foreach ($results[0] as &$result) {
            if ($result[1] !== -1) {
                // Only save the index if it hasn't been set yet
                if ($index === null) {
                    $index = $result[1];
                }

                $result = $result[0];
            } else {
                $result = null;
            }
        }

        $results = $results[0];
        $this->lastIndex += mb_strlen($results[0]) + ($index - $this->lastIndex);

        $matches = new SafeProperties($results);
        $matches->index = isset($index) ? $index : 0;
        $matches->input = $str;

        return $matches;
    }
}

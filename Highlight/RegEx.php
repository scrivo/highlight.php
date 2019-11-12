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
        preg_match_all($this->source, $str, $results, PREG_OFFSET_CAPTURE, $this->lastIndex);

        $manualIndex = false;

        foreach ($results as &$result) {
            if (isset($result[0][0]) && $result[0][0] !== null) {
                $idx = $result[0][1];

                if (isset($result[1][1])) {
                    $nextIndex = $result[1][1];
                    $proposedIndex = $idx + strlen($result[0][0]);
                    $skippedChunk = trim(substr($str, $proposedIndex, $nextIndex - $proposedIndex));

                    $manualIndex = true;

                    if (strlen($skippedChunk)) {
                        $this->lastIndex = $proposedIndex;
                    } else {
                        $this->lastIndex = $nextIndex - 1;
                    }
                }

                if ($idx !== -1) {
                    $result = $result[0][0];

                    if ($index === null) {
                        $index = $idx;
                    }
                } else {
                    $result = null;
                }
            } elseif (isset($result[0]) && $result[0]) {
                $result = $result[0];
            } else {
                $result = null;
            }
        }

        if (!$manualIndex) {
            $this->lastIndex = $index + strlen($results[0]);
        }

        if (count(array_filter($results, function ($v) { return $v !== null; })) === 0) {
            return null;
        }

        $matches = new SafeProperties($results);
        $matches->index = isset($index) ? $index : 0;
        $matches->input = $str;

        return $matches;
    }
}

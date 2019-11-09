<?php

namespace Highlight;

/**
 * @internal
 * @since 9.16.0
 */
final class RegEx extends ArbitraryProperties
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @var int
     */
    public $lastIndex = 0;

    /**
     * @param RegEx|string $regex
     */
    public function __construct($regex)
    {
        $this->regex = (string)$regex;
        $this->data = [];
    }

    public function __toString()
    {
        return (string)$this->regex;
    }

    /**
     * Run the regular expression against the given string.
     *
     * @since 9.16.0
     *
     * @param string $str The string to run this regular expression against.
     *
     * @return PropertyArray|null
     */
    public function exec($str)
    {
        $index = 0;
        $results = [];
        preg_match_all($this->regex, $str, $results, PREG_OFFSET_CAPTURE, $this->lastIndex);

        foreach ($results as &$result) {
            if (isset($result[0][0]) && $result[0][0]) {
                $val = $result[0][0];
                $idx = $result[0][1];
            } elseif (isset($result[0]) && $result[0]) {
                $result = $result[0];
                $idx = 0;
            } else {
                $result = null;
                continue;
            }

            if (($result = $val)) {
                $index = $idx;
            }
        }

        if (count($results) === 0) {
            return null;
        }

        $matches = new PropertyArray($results);
        $matches->index = $index;

        return $matches;
    }
}

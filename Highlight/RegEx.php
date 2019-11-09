<?php


namespace Highlight;

class RegEx extends ArbitraryProperties
{
    private $regex;
    public $lastIndex;

    public function __construct($regex)
    {
        $this->regex = $regex;
        $this->data = [];
    }

    public function __toString()
    {
        return (string)$this->regex;
    }

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

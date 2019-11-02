<?php


namespace Highlight;

class RegEx extends ArbitraryProperties
{
    private $regex;

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
        $results = [];
        preg_match_all($this->regex, $str, $results);

        if (count($results) === 0) {
            return null;
        }

        return new PropertyArray($results);
    }
}

<?php


namespace Highlight;


class PropertyArray extends ArbitraryProperties implements \ArrayAccess, \Countable
{
    private $array;

    public function __construct($data)
    {
        $this->array = $data;
    }

    public function count()
    {
        return count($this->array);
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->array[$offset])) {
            return $this->array[$offset];
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }
}

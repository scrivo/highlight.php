<?php

namespace Highlight;

/**
 * @internal
 *
 * @since 9.16.0.0
 */
final class SafeProperties extends ArbitraryProperties implements \ArrayAccess, \Countable
{
    /**
     * @var IndiscriminateAccess
     */
    private $data;

    /**
     * @param array|\stdClass $data
     */
    public function __construct($data)
    {
        $this->data = new IndiscriminateAccess($data);
    }

    public function &__get($name)
    {
        if (isset($this->customData[$name])) {
            return $this->customData[$name];
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        $null = null;

        return $null;
    }

    public function __isset($name)
    {
        if (isset($this->customData[$name])) {
            return true;
        }

        if (isset($this->data[$name])) {
            return true;
        }

        return false;
    }

    public function count()
    {
        return count($this->data);
    }

    public function offsetExists($offset)
    {
        return isset($this->customData[$offset]) || isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->customData[$offset])) {
            return $this->customData[$offset];
        }

        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->customData[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->customData[$offset]);
    }
}

<?php

namespace Highlight;

/**
 * @internal
 *
 * @since 9.16.0.0
 */
abstract class ArbitraryProperties
{
    protected $customData = array();

    public function __set($name, $value)
    {
        $this->customData[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->customData[$name])) {
            return $this->customData[$name];
        }

        return null;
    }

    public function __isset($name)
    {
        return isset($this->customData[$name]);
    }

    public function __unset($name)
    {
        unset($this->customData[$name]);
    }
}

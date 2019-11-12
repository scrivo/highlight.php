<?php

namespace Highlight;

/**
 * A utility class to access arrays and stdClass objects in the same way.
 *
 * @internal
 *
 * @since 9.16.0.0
 */
final class IndiscriminateAccess implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array|\stdClass
     */
    private $dataSource;

    /**
     * @var bool
     */
    private $isArray;

    public function __construct($dataSource)
    {
        if (!is_object($dataSource) && !is_array($dataSource)) {
            throw new \UnexpectedValueException('Only arrays or stdClass objects may be given as a data source.');
        }

        $this->dataSource = $dataSource;
        $this->isArray = is_array($dataSource);
    }

    public function count()
    {
        if ($this->isArray) {
            return count($this->dataSource);
        }

        return count(get_object_vars($this->dataSource));
    }

    public function offsetExists($offset)
    {
        if ($this->isArray) {
            return isset($this->dataSource[$offset]);
        }

        return isset($this->dataSource->{$offset});
    }

    public function &offsetGet($offset)
    {
        if ($this->isArray) {
            if (isset($this->dataSource[$offset])) {
                return $this->dataSource[$offset];
            }
        } else {
            if (isset($this->dataSource->{$offset})) {
                return $this->dataSource->{$offset};
            }
        }

        $null = null;

        return $null;
    }

    public function offsetSet($offset, $value)
    {
        if ($this->isArray) {
            $this->dataSource[$offset] = $value;
        } else {
            $this->dataSource->{$offset} = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if ($this->isArray) {
            unset($this->dataSource[$offset]);
        } else {
            unset($this->dataSource->{$offset});
        }
    }

    public function getIterator()
    {
        if ($this->isArray) {
            return new \ArrayIterator($this->dataSource);
        }

        return new stdClassIterator($this->dataSource);
    }
}

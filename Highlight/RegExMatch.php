<?php

namespace Highlight;

class RegExMatch implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var array<string | null> */
    private $data;

    /** @var int */
    public $index;

    /** @var string */
    public $input;

    public function __construct(array $results)
    {
        $this->data = $results;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException(__CLASS__ . ' instances are read-only.');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException(__CLASS__ . ' instances are read-only.');
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->data);
    }
}

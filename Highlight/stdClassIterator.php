<?php

namespace Highlight;

final class stdClassIterator implements \Iterator
{
    /**
     * @var \stdClass
     */
    private $dataSource;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string[]
     */
    private $keys;

    public function __construct(\stdClass $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->position = 0;
        $this->keys = get_object_vars($dataSource);
    }

    public function current()
    {
        $index = $this->keys[$this->position];

        return $this->dataSource->{$index};
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->keys[$this->position];
    }

    public function valid()
    {
        return isset($this->keys[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}

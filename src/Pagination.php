<?php

namespace Wordpriest\Modest;

use Illuminate\Support\Collection;

class Pagination implements \ArrayAccess, \JsonSerializable
{
    public $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function paginate($args = [])
    {
        return paginate_links($args);
    }

    public function toArray()
    {
        return $this->items;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Determines whether a offset exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Sets offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed|null|Modest
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}

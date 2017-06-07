<?php

namespace Wordpriest\Modest;

use ArrayAccess;
use Carbon\Carbon;
use JsonSerializable;
use ReflectionClass;

class Modest implements ArrayAccess, JsonSerializable
{
    /**
     * Specified post type
     * If this is not set Modest resolve name via class name
     *
     * @var null|string
     */
    protected $type = null;

    /**
     * Keys that should cast to Carbon\Carbon instances
     *
     * @var array
     */
    protected $dates = [
        'date', 'modified'
    ];

    /**
     * Keys that should remain hidden
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Excerpt length in characters
     *
     * @var integer
     */
    protected $excerptLength = 120;

    /**
     * Attributes
     *
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * Create a new instance
     *
     * @param  \WP_Post $post
     *
     * @return Modest
     */
    public static function make(\WP_Post $post)
    {
        $instance = new static;

        $instance->setAttributes($post);

        return $instance;
    }

    /**
     * Create a new instance with current queried post
     *
     * @return Modest
     */
    public static function current()
    {
        $instance = new static;

        return self::make(get_post([
            'post_type' => $instance->getType()
        ]));
    }

    /**
     * Create a new instance with given post id
     *
     * @param  integer $id
     *
     * @return Modest
     */
    public static function find($id)
    {
        return QueryBuilder::find($id, new static);
    }

    /**
     * @return mixed
     */
    public static function all()
    {
        return QueryBuilder::all(new static);
    }

    /**
     * Creates a new post in database
     *
     * @param  array  $params
     *
     * @return Modest
     */
    public static function create(array $params)
    {
        $instance = new static;

        $params['post_type'] = $instance->getType();

        $id = wp_insert_post($params);

        return self::find($id);
    }

    /**
     * Updates given post in database
     *
     * @param  array  $args
     *
     * @return Modest
     */
    public function update(array $args)
    {
        $params = [];

        foreach ($args as $key => $value) {
            $params[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        $params['ID'] = $this->id;

        return self::create($params);
    }

    /**
     * Saves current instances in database
     *
     * @return Modest
     */
    public function save()
    {
        return self::create($this->toWordpressArray());
    }

    /**
     * Getter for attributes
     *
     * @param $key
     *
     * @return mixed|null|static
     */
    public function get($key)
    {
        // If attribute is defined as hidden null is returned
        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        $value = $this->attributes->get($key);

        // Filter value through the date casting method,
        // it only casts to dates if defined
        $value = $this->castToDates($key, $value);

        // If attribute getter is defined, the value gets filtered via this method
        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        return $value;
    }

    /**
     * Excerpt attribute getter
     * The length is set by the excerptLength param
     *
     * @param $excerpt
     *
     * @return string
     */
    public function getExcerptAttribute($excerpt)
    {
        return $excerpt ?: content_to_excerpt($this->content, $this->excerptLength);
    }

    /**
     * Translates key name to attribute getter name
     *
     * @param $key
     *
     * @return string
     */
    protected function getAttributeMethodName($key)
    {
        $key = snake_to_camel($key);

        return "get{$key}Attribute";
    }

    /**
     * Determines post type based on defined type or class name
     *
     * @return string
     */
    public function getType()
    {
        if ($this->type) {
            return $this->type;
        }

        $reflection = new ReflectionClass($this);

        return camel_to_dash($reflection->getShortName());
    }

    /**
     * Casts defined key values to carbon instances
     *
     * @param $key
     * @param $value
     *
     * @return Carbon
     */
    protected function castToDates($key, $value)
    {
        if (! in_array($key, $this->dates)) {
            return $value;
        }

        return \Carbon\Carbon::create(strtotime($value));
    }

    /**
     * Setter for attributes
     *
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        $collection = [];

        foreach ($attributes as $key => $value) {
            $key = $this->translateAttributeKey($key);

            if (preg_match("/_gmt/", $key)) {
                continue;
            }

            $collection[$key] = $value;
        }

        $this->attributes = new \Illuminate\Support\Collection($collection);
    }

    /**
     * Checks if given attribute key should be hidden
     *
     * @param $key
     *
     * @return bool
     */
    protected function attributeShouldBeHidden($key)
    {
        if (in_array($key, $this->hidden)) {
            return true;
        }

        return false;
    }

    /**
     * Translates attribute keys from Wordpress to Modest
     *
     * @param $key
     *
     * @return string
     */
    protected function translateAttributeKey($key)
    {
        return strtolower(str_replace(['post_', 'menu_'], '', $key));
    }

    /**
     * Translates attribute keys from Modest to Wordpress
     * @param $key
     *
     * @return string
     */
    public function translateAttributeKeyToWordpress($key)
    {
        if ($key == 'id') {
            return strtoupper($key);
        }

        if ($key == 'order') {
            return "menu_{$key}";
        }

        if (in_array($key, ['comment_status', 'ping_status', 'comment_count', 'menu_order'])) {
            return $key;
        }

        return "post_{$key}";
    }

    /**
     * Casts all attributes to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes->except($this->hidden)->toArray();
    }

    /**
     * Casts alla attributes to an Wordpress array
     *
     * @return array
     */
    public function toWordpressArray()
    {
        $items = [];

        foreach ($this->attributes as $key => $value) {
            $items[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        return $items;
    }

    /**
     * Casts alla attributes to json
     *
     * @return string
     */
    public function toJson()
    {
        return $this->attributes->except($this->hidden)->toJson();
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
        return ! is_null($this->get($offset));
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
        return $this->get($offset);
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
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param $method
     * @param $value
     */
    public function __set($method, $value)
    {
        return $this->attributes[$method] = $value;
    }

    /**
     * @param $method
     *
     * @return \Illuminate\Support\Collection|mixed|null|Modest
     */
    public function __get($method)
    {
        if ($method == 'attributes') {
            return $this->attributes;
        }

        return $this->get($method);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return \Wordpriest\Modest\QueryBuilder
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static;

        return (new QueryBuilder($instance))->__call($method, $args);
    }
}

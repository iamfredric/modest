<?php

namespace Wordpriest\Modest;

abstract class Modest
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
     * @return new static
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
     * @return new static
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
     * @return new static
     */
    public static function find($id)
    {
        $instance = new static;

        return self::make(
            get_post([
                'id' => $id,
                'post_type' => $instance->getType()
            ])
        );
    }

    /**
     * Creates a new post in database
     *
     * @param  array  $params
     *
     * @return new static
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
     * @param  integer $id
     * @param  array  $params
     *
     * @return new static
     */
    public function update(array $params)
    {
        $params['ID'] = $this->id;

        return self::create($params);
    }

    public function save()
    {
        // todo...
    }

    public function get($key)
    {
        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        $value = $this->attributes->get($key);
        $value = $this->castToDates($key, $value);

        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        return $value;
    }

    public function getExcerptAttribute($excerpt)
    {
        return $excerpt ?: mb_substr(strip_tags($this->content), $this->excerptLength);
    }

    protected function getAttributeMethodName($key)
    {
        $key = snake_to_camel($key);

        return "get{$key}Attribute";
    }

    public function getType()
    {
        if ($this->type) {
            return $this->type;
        }

        return camel_to_dash(get_class($this));
    }

    protected function castToDates($key, $value)
    {
        if (! in_array($key, $this->dates)) {
            return $value;
        }

        return \Carbon\Carbon::create(strtotime($value));
    }

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

    protected function attributeShouldBeHidden($key)
    {
        if (in_array($key, $this->hidden)) {
            return true;
        }

        return false;
    }

    protected function translateAttributeKey($key)
    {
        return strtolower(str_replace(['post_', 'menu_'], '', $key));
    }

    public function toArray()
    {
        return $this->attributes->except($this->hidden)->toArray();
    }

    public function toJson()
    {
        return $this->attributes->except($this->hidden)->toJson();
    }

    public function __get($method)
    {
        if ($method == 'attributes') {
            return $this->attributes;
        }

        return $this->get($method);
    }
}

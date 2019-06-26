<?php

namespace Wordpriest\Modest;

trait AdvancedCustomFields
{
    /**
     * @var array
     */
    protected $casts = [];

    /**
     * Advanced custom fields getter
     *
     * @param  null $fields
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFieldsAttribute($fields = null)
    {
        if (! $this->attributes->has('fields')) {
            $this->attributes->put('fields', collect(get_fields($this->id)));
        }

        return $this->attributes->get('fields');
    }

    /**
     * @param $key
     *
     * @return mixed|\Wordpriest\Modest\Modest|null
     */
    public function get($key)
    {
        if (! isset($this->casts[$key])) {
            return parent::get($key);
        }

        if (! $this->attributes->has($key)) {
            $this->attributes->put($key, $this->castItem($this->casts[$key], $this->fields->get($key)));
        }

        return parent::get($key);
    }

    /**
     * @param $classname
     * @param $value
     *
     * @return object
     */
    protected function castItem($classname, $value)
    {
        $methodname = $method = 'resolve'.ucfirst(str_replace('\\', '', $classname)).'Cast';

        if (method_exists($this, $methodname)) {
            return $this->{$methodname}($value);
        }

        if (preg_match("/App\\\Models\\\(.*)/", $classname)) {
            return $classname::make($value);
        }

        if ($classname == 'object') {
            return (object) $value;
        }

        return new $classname($value);
    }
}

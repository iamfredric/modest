<?php

namespace Wordpriest\Modest;

trait AdvancedCustomFields
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * Advanced custom fields getter
     *
     * @param  null $fields
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFieldsAttribute($fields)
    {
        if (count($this->fields)) {
            return $this->fields;
        }

        return $this->fields = collect(get_fields($this->id));
    }
}

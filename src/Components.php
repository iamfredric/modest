<?php

namespace Wordpriest\Modest;

use Incognito\Components\Components as ComponentsBundler;

trait Components
{
    /**
     * @param $components
     *
     * @return \Incognito\Components\Components
     */
    public function getComponentsAttribute($components)
    {
        return $this->components('components');
    }

    /**
     * @param null $prefix
     *
     * @return \Incognito\Components\Components
     */
    public function components($fieldname = 'components', $prefix = null)
    {
        $key = $prefix ? "{$prefix}-components" : 'components';

        if (! $this->attributes->has($key)) {
            $this->attributes->put($key, new ComponentsBundler($this->fields->get($fieldname), $prefix));
        }

        return $this->attributes->get($key);
    }
}

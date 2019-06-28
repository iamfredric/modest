<?php

namespace Wordpriest\Modest;

use Incognito\Components\Components as ComponentsBundler;

class Components
{
    /**
     * @param $components
     *
     * @return \Incognito\Components\Components
     */
    public function getComponentsAttribute($components)
    {
        if (! $this->attributes->has('components')) {
            $this->attributes->put('components', new ComponentsBundler($this->fields->get('components')));
        }

        return $this->attributes->get('components');
    }

    /**
     * @param null $prefix
     *
     * @return \Incognito\Components\Components
     */
    public function components($prefix = null)
    {
        $key = $prefix ? "{$prefix}-components" : 'components';

        if (! $this->attributes->has($key)) {
            $this->attributes->put($key, new ComponentsBundler($this->fields->get('components'), $prefix));
        }

        return $this->attributes->get($key);
    }
}

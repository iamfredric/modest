<?php

namespace Wordpriest\Modest;

use Incognito\Utilities\Wordpress\Image;
use Incognito\Utilities\Wordpress\WpImage;

trait Thumbnail
{
    /**
     * @return \Incognito\Utilities\Wordpress\Image | \Incognito\Utilities\Wordpress\WpImage
     */
    protected function getThumbnailAttribute()
    {
        if ($this->fields->has('thumbnail')) {
            return new Image($this->fields->get('thumbnail'));
        }

        return new WpImage($this->id);
    }
}

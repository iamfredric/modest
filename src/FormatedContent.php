<?php

namespace Wordpriest\Modest;

trait FormatedContent
{
    /**
     * @param $content
     *
     * @return mixed
     */
    public function getContentAttribute($content)
    {
        return apply_filters('the_content', $content);
    }
}

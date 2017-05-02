<?php

namespace Wordpriest\Modest;

class QueryBuilder
{
    protected $arguments = [];

        public $dev = [
        // 'attachment',
        // 'attachment_id',
        'author',
        'author_name',
        'cat',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_popup',
        'day',
        'error',
        'feed',
        'hour',
        'm',
        'minute',
        'monthnum',
        'name',
        'order',
        'orderby',
        'p',
        'page_id',
        'page',
        'paged',
        'pagename',
        'post__in',
        'post__not_in',
        'post_status',
        'post_type',
        'preview',
        'robots',
        's',
        'sentence',
        'second',
        'static',
        'subpost',
        'subpost_id',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'tag'
    ];

    public function buildWhere($key, $value)
    {
        $this->setArgument($key, $value);

        return $this;
    }

    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    protected function resolveMethodCall($method, $args)
    {
        list($key, $value) = $this->resolveArgumentsFromMethodCall($method, $args);

        return $this->buildWhere($key, $value);
    }

    public function resolveArgumentsFromMethodCall($method, $arguments, $args = [])
    {
        if ($method = str_replace('where', '', $method)) {
            $args[] = strtolower($method);
        }

        foreach ($arguments as $argument) {
            $args[] = $argument;
        }

        return $args;
    }

    public function __call($method, $args)
    {
        return $this->resolveMethodCall($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        $instance = new static;

        return $instance->__call($method, $args);
    }


    // Query::whereAuthor(10)
    // Query::where('author', 10)
    // Query::whereAuthorIn([10, 11])
    // Query::whereAuthorNotIn([12, 14])
    // Query::where('author', '!=', 12)
}
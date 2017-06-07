<?php

namespace Wordpriest\Modest;

use Illuminate\Support\Collection;

class QueryBuilder
{
    /**
     * Query arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Meta query arguments
     *
     * @var array
     */
    protected $metaArguments = [];

    /**
     * @var null|Modest
     */
    protected $model = null;

    /**
     * QueryBuilder constructor.
     *
     * @param \Wordpriest\Modest\Modest|null $model
     */
    public function __construct(Modest $model = null)
    {
        $this->model = $model;

        if ($model) {
            $this->setArgument('post_type', $model->getType());
        }
    }

    /**
     * Fetches the first record from database
     *
     * @return Modest
     */
    public function first()
    {
        $this->setArgument('posts_per_page', 1);

        $posts = get_posts($this->getArguments());

        return $this->buildItem($posts[0]);
    }

    /**
     * Fetches database record based on id
     *
     * @param $id
     *
     * @return mixed
     */
    public static function find($id, $model = null)
    {
        $instance = new static($model);

        $instance->arguments = [];
        $instance->setArgument('id', $id);

        return $instance->first();
    }

    /**
     * Fetches the items from the database based on given queries
     *
     * @return Collection
     */
    public function get()
    {
        $posts = new Collection();

        foreach ((array) get_posts($this->getArguments()) as $post) {
            $posts->push($this->buildItem($post));
        }

        return $posts;
    }

    protected function buildItem($post)
    {
        if ($this->model) {
            $class = get_class($this->model);

            return $class::make($post);
        }

        return Modest::make($post);
    }

    /**
     * Fetches all items from database
     *
     * @param null $model
     *
     * @return mixed
     */
    public static function all($model = null)
    {
        $instance = new static($model);

        $instance->setArgument('posts_per_page', -1);

        return $instance->get();
    }

    /**
     * Getter for arguments
     *
     * @return array
     */
    public function getArguments()
    {
        $args = $this->arguments;

        if (count($this->metaArguments)) {
            $args['meta_query'] = $this->metaArguments;
        }

        return $args;
    }

    /**
     * Setter for argument
     *
     * @param $key
     * @param $value
     */
    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;
    }

    /**
     * Query builder
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    protected function buildWhere($key, $value)
    {
        $this->setArgument($key, $value);

        return $this;
    }

    /**
     * Metaquery builder
     *
     * @param $args
     *
     * @return $this
     */
    protected function buildMetaWhere($args)
    {
        $key = $args[0];
        $compare = count($args) == 3 ? $args[1] : '=';
        $value = count($args) == 3 ? $args[2] : $args[1];

        $this->metaArguments[] = [
            'meta_key' => $key,
            'meta_value' => $value,
            'meta_compare' => $compare
        ];

        return $this;
    }

    /**
     * Resolves what magic method is called
     *
     * @param $method
     * @param $args
     *
     * @return QueryBuilder
     */
    protected function resolveMethodCall($method, $args)
    {
        list($key, $value) = $this->resolveArgumentsFromMethodCall($method, $args);

        if ($key == 'meta') {
            return $this->buildMetaWhere($args);
        }

        return $this->buildWhere($key, $value);
    }

    /**
     * Resolves arguments from magic method call
     *
     * @param $method
     * @param $arguments
     * @param array $args
     *
     * @return array
     */
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

    /**
     * @param $method
     * @param $args
     *
     * @return QueryBuilder
     */
    public function __call($method, $args)
    {
        return $this->resolveMethodCall($method, $args);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return QueryBuilder|Collection|Modest
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static;

        return $instance->__call($method, $args);
    }
}
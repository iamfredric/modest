<?php

function get_post() {
    return new WP_Post;
}

function get_posts($args) {
    $limit = isset($args['posts_per_page']) ? $args['posts_per_page'] : 3;

    $posts = [];

    foreach (range(1, $limit) as $index) {
        $posts[] = new WP_Post;
    }

    return $posts;
}

function wp_insert_post() {
    return true;
}

class Page extends \Wordpriest\Modest\Modest
{
    public function getTitleAttribute($value)
    {
        return "Changed {$value}";
    }
}

class AreaGuide extends \Wordpriest\Modest\Modest
{}

class Post extends \Wordpriest\Modest\Modest
{
    protected $type = 'random';
}

class WP_Post
{
    public $ID = 1;

    public $post_author = 2;

    public $post_name = 'lorem';

    public $post_type = 'type';

    public $post_title = 'Title';

    public $post_date = '2017-04-29 10:55:01';

    public $post_date_gmt = '2017-04-29 09:55:01';

    public $post_content = 'In odio tellus, pellentesque non.';

    public $post_excerpt = '';

    public $post_status = 'publish';

    public $comment_status = 'open';

    public $ping_status = 'open';

    public $post_password = 'secret';

    public $post_parent = 0;

    public $post_modified = '2017-04-29 10:55:01';

    public $post_modified_gmt = '2017-04-29 09:55:01';

    public $comment_count = 10;

    public $menu_order = 3;
}

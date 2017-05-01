<?php

use PHPUnit\Framework\TestCase;

class PostArrayableTest extends TestCase
{
    /** @test */
    public function it_can_be_accessed_as_an_array()
    {
        $post = Page::current();

        $this->assertEquals($post['id'], $post->get('id'));
    }

    /** @test */
    public function it_can_set_an_item_from_an_array()
    {
        $post = Page::current();

        $this->assertEquals($post->get('id'), 1);

        $post['id'] = 123;

        $this->assertEquals($post->get('id'), 123);
    }

    /** @test */
    public function it_can_get_all_attributes_as_an_array()
    {
        $post = Page::current();

        $this->assertArrayHasKey('id', $post->toArray());
    }

    /** @test */
    public function it_can_translate_array_to_wordpress_keys()
    {
        $post = Page::current();

        $array = $post->toWordpressArray();

        foreach ([
            'ID', 'post_author', 'post_name', 'post_type', 'post_title', 'post_date', 'post_content', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_password', 'post_parent', 'post_modified', 'comment_count', 'menu_order'
                 ] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }
}
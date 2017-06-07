<?php

use PHPUnit\Framework\TestCase;

class PostQueryTest extends TestCase
{
    /** @test */
    function a_modest_object_can_query_database()
    {
        $post = Post::where('testkey', 'testvalue');

        $args = $post->getArguments();

        $this->assertInstanceOf(\Wordpriest\Modest\QueryBuilder::class, $post);

        $this->assertEquals($args['testkey'],'testvalue');
        $this->assertEquals($args['post_type'], 'random');
    }

    /** @test */
    function a_single_item_query_returns_an_instance_of_given_class()
    {
        $page = Page::where('id', 2)->first();

        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    function a_posts_query_return_an_array_of_given_class()
    {
        $posts = Page::where('post_parent', 20)->get();

        $this->assertEquals(count($posts), 3);

        $this->assertInstanceOf(Page::class, $posts[0]);
    }

    /** @test */
    function a_post_can_be_modified_and_saved()
    {
        $post = Post::current();

        $post->title = 'New title';

        $savedPost = $post->save();

        $this->assertInstanceOf(Post::class, $savedPost);
    }

    /** @test */
    function all_posts_can_be_queried_static()
    {
        $posts = Post::all();

        $this->assertEquals(count($posts), 3);

        $this->assertInstanceOf(Post::class, $posts[0]);
    }

    /** @test */
    function it_can_fetch_a_single_post_by_id()
    {
        $post = Post::find(1);

        $this->assertInstanceOf(Post::class, $post);
    }
}
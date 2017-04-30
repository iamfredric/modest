<?php

use PHPUnit\Framework\TestCase;

class PostSetupTest extends TestCase
{
    /** @test */
    public function it_returns_a_new_post_instance_when_making_a_post()
    {
        $post = Page::make(new WP_Post);

        $this->assertTrue($post instanceof Page);
    }

    /** @test */
    public function it_returns_a_new_post_instance_when_fetching_current_post()
    {
        $post = Page::current();

        $this->assertTrue($post instanceof Page);
    }

    /**
     * @test
     */
    public function it_find_a_given_post_by_its_id()
    {
        $post = Page::find(1);

        $this->assertTrue($post instanceof Page);
    }

    /**
     * @test
     */
    public function it_returns_a_post_when_creating_a_new_post()
    {
        $post = Page::create([]);

        $this->assertTrue($post instanceof Page);
    }

    /**
     * @test
     */
    public function it_returns_a_post_when_updating_a_post()
    {
        $post = Page::find([]);

        $updatedPost = $post->update([]);

        $this->assertTrue($updatedPost instanceof Page);
    }
}

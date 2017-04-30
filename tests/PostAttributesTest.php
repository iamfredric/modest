<?php

use PHPUnit\Framework\TestCase;

class PostAttributesTest extends TestCase
{
    protected $post;

    public function setUp()
    {
        $this->post = Page::make(new WP_Post);
    }

    /** @test */
    public function it_casts_attributes_to_a_collection()
    {
        $this->assertTrue($this->post->attributes instanceof \Illuminate\Support\Collection);
    }

    /**
     * @test
     */
    public function it_can_fetch_attributes_via_methods_and_params()
    {
        $this->assertEquals($this->post->get('id'), 1);
        $this->assertEquals($this->post->id, 1);
    }

    /**
     * @test
     */
    public function it_casts_dates_to_carbon_instances()
    {
        $date = $this->post->date;

        $this->assertTrue($date instanceof \Carbon\Carbon);
    }

    /**
     * @test
     */
    public function it_can_modify_attributes()
    {
        $this->assertEquals($this->post->attributes['title'], 'Title');

        $this->assertEquals($this->post->title, 'Changed Title');
    }

    /** @test */
    public function it_hides_defined_attributes()
    {
        $this->assertEquals('secret', $this->post->attributes['password']);
        $this->assertEquals(null, $this->post->password);
    }

    /** @test */
    public function it_can_be_casted_to_json()
    {
        $object = json_decode($this->post->toJson());

        $this->assertTrue(is_object($object));
        $this->assertFalse(isset($object->password));
    }

    /** @test */
    public function it_can_change_attributes_values()
    {
        $this->post->title = 'Updated title';

        $this->assertEquals($this->post->title, 'Updated title');
    }
}

<?php

use PHPUnit\Framework\TestCase;

class PosttypeTest extends TestCase
{
    /** @test */
    public function it_determins_posttype_based_on_class_name()
    {
        $class = new Page;

        $this->assertEquals($class->getType(), 'page');
    }

    /** @test */
    public function it_converts_camelcase_to_dash_separated_post_type_name()
    {
        $class = new AreaGuide;

        $this->assertEquals($class->getType(), 'area-guide');
    }

    /**
     * @test
     */
    public function if_type_is_defined_it_overrides_default_classname()
    {
        $class = new Post;

        $this->assertEquals($class->getType(), 'random');
    }
}

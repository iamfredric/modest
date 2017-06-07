<?php

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function it_can_be_called_staticly_and_chained()
    {
        $builder = \Wordpriest\Modest\QueryBuilder::where('test', 'lorem');
        $args = $builder->getArguments();

        $this->assertEquals($args['test'], 'lorem');

        $builder = \Wordpriest\Modest\QueryBuilder::whereTest('lorem');
        $args = $builder->getArguments();

        $this->assertEquals($args['test'], 'lorem');
    }

    /** @test */
    public function it_can_be_chained()
    {
        $builder = \Wordpriest\Modest\QueryBuilder::whereTest('yes')
            ->whereLorem('dolor');

        $args = $builder->getArguments();

        $this->assertEquals($args['test'], 'yes');
        $this->assertEquals($args['lorem'], 'dolor');

        $builder = \Wordpriest\Modest\QueryBuilder::where('test', 'yes')
            ->where('lorem', 'dolor');

        $args = $builder->getArguments();

        $this->assertEquals($args['test'], 'yes');
        $this->assertEquals($args['lorem'], 'dolor');
    }

    /** @test */
    function it_can_query_metadata()
    {
        $builder = \Wordpriest\Modest\QueryBuilder::whereTest('yes')
                  ->whereMeta('metakey', '=', 'metavalue');

        $args = $builder->getArguments();

        $meta = $args['meta_query'][0];

        $this->assertEquals($meta['meta_key'], 'metakey');
        $this->assertEquals($meta['meta_value'], 'metavalue');
        $this->assertEquals($meta['meta_compare'], '=');
    }

    /** @test */
    function when_metadata_is_queried_without_compare_it_defaults_to_equals()
    {
        $builder = \Wordpriest\Modest\QueryBuilder::whereTest('yes')
                                                  ->whereMeta('metakey', 'metavalue');

        $args = $builder->getArguments();

        $meta = $args['meta_query'][0];

        $this->assertEquals($meta['meta_key'], 'metakey');
        $this->assertEquals($meta['meta_value'], 'metavalue');
        $this->assertEquals($meta['meta_compare'], '=');
    }

    /** @test */
    function it_can_compare_metadata_with_another_compare_delimiter()
    {
        $builder = \Wordpriest\Modest\QueryBuilder::whereTest('yes')
                                                  ->whereMeta('metakey', '!=', 'metavalue');

        $args = $builder->getArguments();

        $meta = $args['meta_query'][0];

        $this->assertEquals($meta['meta_key'], 'metakey');
        $this->assertEquals($meta['meta_value'], 'metavalue');
        $this->assertEquals($meta['meta_compare'], '!=');
    }

    /** @test */
    function it_gets_all_the_posts()
    {
        $results = \Wordpriest\Modest\QueryBuilder::where('id', 1)->get();

        $this->assertEquals(count($results), 3);
        $this->assertInstanceOf(\Wordpriest\Modest\Modest::class, $results[0]);
    }

    /** @test */
    function it_fetches_the_first_post()
    {
        $results = \Wordpriest\Modest\QueryBuilder::where('id', 1)->first();

        $this->assertEquals(count($results), 1);
        $this->assertInstanceOf(\Wordpriest\Modest\Modest::class, $results);
    }

    /** @test */
    function it_finds_a_specific_post_based_on_id()
    {
        $results = \Wordpriest\Modest\QueryBuilder::find(1);

        $this->assertEquals(count($results), 1);
        $this->assertInstanceOf(\Wordpriest\Modest\Modest::class, $results);
    }

    /** @test */
    function it_fetches_all_posts()
    {
        $results = \Wordpriest\Modest\QueryBuilder::all();

        $this->assertEquals(count($results), 3);
        $this->assertInstanceOf(\Wordpriest\Modest\Modest::class, $results[0]);
    }
}
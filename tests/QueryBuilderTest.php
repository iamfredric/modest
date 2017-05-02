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
}
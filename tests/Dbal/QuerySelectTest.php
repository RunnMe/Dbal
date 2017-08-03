<?php

namespace Runn\tests\Dbal\Query;

use Runn\Dbal\Query;

class QuerySelectTest extends \PHPUnit_Framework_TestCase
{

    public function testColumns()
    {
        $query = new Query();
        $q = $query->select();

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame('select', $query->action);

        $query = new Query();
        $q = $query->select('*');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame('select', $query->action);

        $query = new Query();
        $q = $query->select()->column('*');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['*'], $query->columns);
        $this->assertSame('select', $query->action);

        $query = new Query();

        $q = $query->select()->column('foo1');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo1'], $query->columns);
        $this->assertSame('select', $query->action);

        $q = $q->column('bar1');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['foo1', 'bar1'], $query->columns);
        $this->assertSame('select', $query->action);

        $q = $q->columns('baz11, baz12');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['baz11', 'baz12'], $query->columns);
        $this->assertSame('select', $query->action);

        $q = $q->columns(['baz21', 'baz22']);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame(['baz21', 'baz22'], $query->columns);
        $this->assertSame('select', $query->action);
    }

    public function testTablesAndFrom()
    {
        $query = new Query();

        $q = $query->select()->table('foo');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);

        $q = $query->select()->table('bar');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->select()->tables('foo1, `bar1`');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo1', 'bar1'], $query->tables);

        $q = $query->select()->tables('foo', '`bar`');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->select()->tables(['foo1', '`bar1`']);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo1', 'bar1'], $query->tables);

        $q = $query->select()->from('foo', 'bar');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->select()->from('foo.bar');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo.bar'], $query->tables);

        $q = $query->select()->from('"foo"."bar"');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo.bar'], $query->tables);
    }

    public function testWith()
    {
        $query = new Query();

        $expectations = [
            [
                'args'   => ['foo'],
                'result' => ['foo']
            ],
            [
                'args'   => ['foo1, bar1'],
                'result' => ['foo1', 'bar1']
            ],
            [
                'args'   => ['foo2', 'bar2'],
                'result' => ['foo2', 'bar2']
            ],
            [
                'args'   => ['foo3, `bar3`'],
                'result' => ['foo3', 'bar3']
            ],
            [
                'args'   => ['foo4', '"bar4"'],
                'result' => ['foo4', 'bar4']
            ],
            [
                'args'   => ['foo.bar'],
                'result' => ['foo.bar']
            ],
            [
                'args'   => ['"foo"."bar"'],
                'result' => ['foo.bar']
            ],
            [
                'args'   => ['"foo" AS "bar"'],
                'result' => ['"foo" AS "bar"']
            ],
        ];

        foreach ($expectations as $exp) {

            $q = $query->with(...$exp['args'])->select();

            $this->assertInstanceOf(Query::class, $q);
            $this->assertSame($q, $query);
            $this->assertEquals('select', $query->action);
            $this->assertEquals(['*'], $query->columns);
            $this->assertEquals($exp['result'], $query->with);

        }

    }

    public function testJoins()
    {
        $query = new Query();
        $query->select()->from('foo');

        $q = $query->join('bar', 'bar.id=foo.bar_id');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
        ], $query->joins);

        $q = $query->join('baz', 'baz.id=foo.baz_id', 'left');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
        ], $query->joins);

        $q = $query->join('bla', 'bla.id=foo.bla_id', 'right', 'b');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals([
            ['table' => 'bar', 'on' => 'bar.id=foo.bar_id', 'type' => 'full'],
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ], $query->joins);

        $q = $query->joins([
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ]);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals([
            ['table' => 'baz', 'on' => 'baz.id=foo.baz_id', 'type' => 'left'],
            ['table' => 'bla', 'on' => 'bla.id=foo.bla_id', 'type' => 'right', 'alias' => 'b'],
        ], $query->joins);
    }

    public function testWhere()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->where('foo.id=1');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('foo.id=1', $query->where);
    }

    public function testGroup()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->where('foo.id=1')->group('id');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('foo.id=1', $query->where);
        $this->assertEquals(['id'], $query->group);

        $q = $query->select()->from('foo')->group('id, name');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['id', 'name'], $query->group);
    }

    public function testHaving()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->group('id')->having('name=:name');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['id'], $query->group);
        $this->assertEquals('name=:name', $query->having);
    }

    public function testOrder()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->order('foo.id');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['foo.id'], $query->order);

        $q = $query->select()->from('foo')->order('id, `name`');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['id', '`name`'], $query->order);

        $q = $query->select()->from('foo')->order('"id" DESC, `name` asc');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['"id" DESC', '`name` asc'], $query->order);
    }

    public function testOffsetLimit()
    {
        $query = new Query();

        $q = $query->select()->from('foo')->offset(10);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(10, $query->offset);

        $q = $query->select()->from('foo')->limit(20);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(20, $query->limit);
    }

}
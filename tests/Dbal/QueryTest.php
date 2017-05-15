<?php

namespace Runn\tests\Dbal\Query;

use Runn\Core\Std;
use Runn\Dbal\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{

    public function testTrimName()
    {
        $query = new Query();
        $reflector = new \ReflectionMethod($query, 'trimName');
        $reflector->setAccessible(true);

        $this->assertEquals(
            'foo',
            $reflector->invokeArgs($query, ['foo'])
        );
        $this->assertEquals(
            'foo',
            $reflector->invokeArgs($query, ['`foo`'])
        );
        $this->assertEquals(
            'foo',
            $reflector->invokeArgs($query, ['"foo"'])
        );
        $this->assertEquals(
            'foo',
            $reflector->invokeArgs($query, [' `"foo"`  '])
        );

        $this->assertEquals(
            '`foo` AS `bar`',
            $reflector->invokeArgs($query, ['`foo` AS `bar`'])
        );
        $this->assertEquals(
            '"baz" DESC',
            $reflector->invokeArgs($query, ['"baz" DESC'])
        );

        $this->assertEquals(
            'foo.bar',
            $reflector->invokeArgs($query, ['`foo`.`bar`'])
        );
        $this->assertEquals(
            'foo.bar.baz',
            $reflector->invokeArgs($query, ['"foo"."bar".baz'])
        );
    }

    public function testPrepareNames()
    {
        $query = new Query();
        $reflector = new \ReflectionMethod($query, 'prepareNames');
        $reflector->setAccessible(true);

        $this->assertEquals(
            [],
            $reflector->invokeArgs($query, [[]])
        );

        $this->assertEquals(
            ['*'],
            $reflector->invokeArgs($query, [['*']])
        );

        $this->assertEquals(
            ['foo'],
            $reflector->invokeArgs($query, [['foo']])
        );
        $this->assertEquals(
            ['foo'],
            $reflector->invokeArgs($query, [[' `"foo"`  ']])
        );

        $this->assertEquals(
            ['foo', 'bar'],
            $reflector->invokeArgs($query, [['foo, bar']])
        );
        $this->assertEquals(
            ['foo', 'bar'],
            $reflector->invokeArgs($query, [['foo', 'bar']])
        );
        $this->assertEquals(
            ['foo', 'bar'],
            $reflector->invokeArgs($query, [[' `"foo, bar"`  ']])
        );
        $this->assertEquals(
            ['foo', 'bar'],
            $reflector->invokeArgs($query, [[' `"foo', 'bar"`  ']])
        );

        $this->assertEquals(
            ['foo AS f', '`bar` b'],
            $reflector->invokeArgs($query, [['foo AS f, `bar` b']])
        );
    }

    public function testIsString()
    {
        $query = (new Query)->select()->from('table');
        $this->assertFalse($query->isString());

        $query = new Query('SELECT * FROM table');
        $this->assertTrue($query->isString());
    }

    public function testColumns()
    {
        $query = new Query();
        $q = $query->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals('select', $query->action);

        $query = new Query();
        $q = $query->select('*');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals('select', $query->action);

        $query = new Query();
        $q = $query->select()->column('*');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals('select', $query->action);

        $query = new Query();

        $q = $query->select()->column('foo1');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals(['foo1'], $query->columns);
        $this->assertEquals('select', $query->action);

        $q = $q->column('bar1');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals(['foo1', 'bar1'], $query->columns);
        $this->assertEquals('select', $query->action);
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

        $q = $query->select()->tables('foo, `bar`');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->select()->tables('foo', '`bar`');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->select()->tables(['foo', '`bar`']);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->tables);

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

        $q = $query->with('foo')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo'], $query->with);

        $q = $query->with('bar')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['bar'], $query->with);

        $q = $query->with('foo, `bar`')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->with);

        $q = $query->with('foo', '`bar`')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->with);

        $q = $query->with(['foo', '`bar`'])->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->with);

        $q = $query->with('foo', 'bar')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo', 'bar'], $query->with);

        $q = $query->with('foo.bar')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo.bar'], $query->with);

        $q = $query->with('"foo"."bar"')->select();
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['*'], $query->columns);
        $this->assertEquals(['foo.bar'], $query->with);
    }

    public function testAction()
    {
        $query = new Query();

        $q = $query->into('foo');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);

        $q = $query->update('foo, bar');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('update', $query->action);
        $this->assertEquals(['foo', 'bar'], $query->tables);

        $q = $query->delete(['foo', 'bar']);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('delete', $query->action);
        $this->assertEquals(['foo', 'bar'], $query->tables);
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

    public function testValue()
    {
        $query = new Query();

        $q = $query->into('foo')->value('a', 1);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['a' => 1], $query->values);

        $q = $query->into('foo')->value('"b" ', 2);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['a' => 1, 'b' => 2], $query->values);
    }

    public function testInsert()
    {
        $query = new Query();

        $q = $query->into('foo')->insert([]);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals([], $query->values);

        $q = $query->into('foo')->insert(['a' => 1, 'b' => 2]);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['a' => 1, 'b' => 2], $query->values);

        $q = $query->into('foo')->insert([' `a`' => 1, '"b" ' => 2]);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals(['a' => 1, 'b' => 2], $query->values);
    }

    public function testParam()
    {
        $query = new Query();
        $q = $query->select()->from('foo')->where('id=:id')->param('id', 1);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals(['id' => 1], $query->params);

        $q = $query->param(':bar', 'baz');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals(['id' => 1, ':bar' => 'baz'], $query->params);
    }

    public function testParams()
    {
        $query = new Query();
        $q = $query->select()->from('foo')->where('id=:id')->params(['id' => 1, ':bar' => 'baz']);

        $this->assertInstanceOf(Query::class, $q);

        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals(['id' => 1, ':bar' => 'baz'], $query->params);
    }

    public function testFromString()
    {
        $query = new Query('SELECT * FROM foo WHERE bar=42');

        $this->assertInstanceOf(Query::class, $query);
        $this->assertTrue($query->isString());
        $this->assertEquals('SELECT * FROM foo WHERE bar=42', $query->string);
    }

    public function testFromArray()
    {
        $query = new Query([
            'action' => 'select',
            'columns' => 'foo, `bar`, "baz" AS b, "public.table1"',
            'tables' => ' tbl  ',
            'joins' => [
                ['table' => 'tb1', 'on' => 'tb1.id=tbl.baz_id', 'type' => 'left'],
                ['table' => 'tb2', 'on' => 'tb2.id=tbl.bla_id', 'type' => 'right', 'alias' => 'b2'],
            ],
            'where' => 'id=:id',
            'group' => 'grp1, grp2',
            'having' => 'name=:name',
            'order' => 'created DESC',
            'offset' => 20,
            'limit' => 10,
            'values' => ['`id`' => 1, 'name' => 'Test'],
            'params' => [':id' => 11, ':name' => 'Test1'],
            'attr1' => 'val1',
        ]);

        $this->assertInstanceOf(Query::class, $query);

        $this->assertFalse($query->isString());

        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo', 'bar', '"baz" AS b', 'public.table1'], $query->columns);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals([
            ['table' => 'tb1', 'on' => 'tb1.id=tbl.baz_id', 'type' => 'left'],
            ['table' => 'tb2', 'on' => 'tb2.id=tbl.bla_id', 'type' => 'right', 'alias' => 'b2'],
        ], $query->joins);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals(['grp1', 'grp2'], $query->group);
        $this->assertEquals(['created DESC'], $query->order);
        $this->assertEquals(20, $query->offset);
        $this->assertEquals(10, $query->limit);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $query->values);
        $this->assertEquals([':id' => 11, ':name' => 'Test1'], $query->params);
        $this->assertEquals('val1', $query->attr1);
    }

    public function testFromArrayable()
    {
        $options = new Std([
            'action' => 'select',
            'columns' => 'foo, `bar`, "baz" AS b, "public.table1"',
            'tables' => ' tbl  ',
            'joins' => [
                ['table' => 'tb1', 'on' => 'tb1.id=tbl.baz_id', 'type' => 'left'],
                ['table' => 'tb2', 'on' => 'tb2.id=tbl.bla_id', 'type' => 'right', 'alias' => 'b2'],
            ],
            'where' => 'id=:id',
            'group' => 'grp1, grp2',
            'having' => 'name=:name',
            'order' => 'created DESC',
            'offset' => 20,
            'limit' => 10,
            'values' => ['`id`' => 1, 'name' => 'Test'],
            'params' => [':id' => 11, ':name' => 'Test1'],
            'attr1' => 'val1',
        ]);

        $query = new Query($options);

        $this->assertInstanceOf(Query::class, $query);

        $this->assertFalse($query->isString());

        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo', 'bar', '"baz" AS b', 'public.table1'], $query->columns);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals([
            ['table' => 'tb1', 'on' => 'tb1.id=tbl.baz_id', 'type' => 'left'],
            ['table' => 'tb2', 'on' => 'tb2.id=tbl.bla_id', 'type' => 'right', 'alias' => 'b2'],
        ], $query->joins);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals(['grp1', 'grp2'], $query->group);
        $this->assertEquals(['created DESC'], $query->order);
        $this->assertEquals(20, $query->offset);
        $this->assertEquals(10, $query->limit);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $query->values);
        $this->assertEquals([':id' => 11, ':name' => 'Test1'], $query->params);
        $this->assertEquals('val1', $query->attr1);
    }

}
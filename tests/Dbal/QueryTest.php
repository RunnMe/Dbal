<?php

namespace Runn\tests\Dbal\Query;

use PHPUnit\Framework\TestCase;
use Runn\Core\Std;
use Runn\Dbal\Dbh;
use Runn\Dbal\Query;

class QueryTest extends TestCase
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

    public function testFromString()
    {
        $query = new Query('SELECT * FROM foo WHERE bar=42');

        $this->assertInstanceOf(Query::class, $query);
        $this->assertTrue($query->isString());
        $this->assertEquals('SELECT * FROM foo WHERE bar=42', $query->string);
    }

    public function testAction()
    {
        $query = new Query();

        $q = $query->select()->from('foo');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('select', $query->action);
        $this->assertSame(['foo'], $query->tables);

        $q = $query->insert(['bar' => 42]);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('insert', $query->action);
        $this->assertSame(['bar' => 42], $query->values);

        $q = $query->into('foo');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('insert', $query->action);
        $this->assertSame(['foo'], $query->tables);

        $q = $query->update('foo, bar');
        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('update', $query->action);
        $this->assertSame(['foo', 'bar'], $query->tables);

        $q = $query->delete(['foo', 'bar']);
        $this->assertInstanceOf(Query::class, $q);
        $this->assertSame($q, $query);
        $this->assertSame('delete', $query->action);
        $this->assertSame(['foo', 'bar'], $query->tables);
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
        $this->assertEquals([
            ['name' => ':id', 'value' => 11, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':name', 'value' => 'Test1', 'type' => Dbh::DEFAULT_PARAM_TYPE],
        ], $query->params);
        $this->assertEquals('val1', $query->attr1);

        $query = new Query([
            'select' => 'foo, `bar`, "baz" AS b, "public.table1"',
            'tables' => ' tbl  ',
            'attr1' => 'val1',
        ]);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertFalse($query->isString());

        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo', 'bar', '"baz" AS b', 'public.table1'], $query->columns);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals('val1', $query->attr1);

        $query = new Query([
            'into' => 'tbl',
            'insert' => ['`id`' => 1, 'name' => 'Test'],
        ]);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertFalse($query->isString());
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $query->values);

        $query = new Query([
            'update' => 'tbl',
            'set' => ['`id`' => 1, 'name' => 'Test'],
        ]);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertFalse($query->isString());
        $this->assertEquals('update', $query->action);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $query->values);
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
        $this->assertEquals([
            ['name' => ':id', 'value' => 11, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':name', 'value' => 'Test1', 'type' => Dbh::DEFAULT_PARAM_TYPE],
        ], $query->params);
        $this->assertEquals('val1', $query->attr1);

        $options = new Std([
            'into' => 'tbl',
            'insert' => ['`id`' => 1, 'name' => 'Test'],
        ]);

        $query = new Query($options);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertFalse($query->isString());
        $this->assertEquals('insert', $query->action);
        $this->assertEquals(['tbl'], $query->tables);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $query->values);
    }

}

<?php

namespace Runn\tests\Dbal\Query;

use PHPUnit\Framework\TestCase;
use Runn\Core\Std;
use Runn\Dbal\Dbh;
use Runn\Dbal\Query;

class QueryParamsTest extends TestCase
{

    public function testEmptyParamsIsArray()
    {
        $query = new Query;
        $this->assertSame([], $query->params);
    }

    public function testParam()
    {
        $query = new Query();
        $q = $query->select()->from('foo')->where('id=:id')->param(':id', 1);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE]
        ], $query->params);

        $q = $query->param(':bar', 'baz');

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bar', 'value' => 'baz', 'type' => Dbh::DEFAULT_PARAM_TYPE],
        ], $query->params);

        $q = $query->param(':bla', 42, Dbh::PARAM_INT);

        $this->assertInstanceOf(Query::class, $q);
        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bar', 'value' => 'baz', 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bla', 'value' => 42, 'type' => Dbh::PARAM_INT],
        ], $query->params);
    }

    public function testParams()
    {
        $query = new Query();
        $q = $query->select()->from('foo')->where('id=:id')->params([':id' => 1, ':bar' => 'baz']);

        $this->assertInstanceOf(Query::class, $q);

        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bar', 'value' => 'baz', 'type' => Dbh::DEFAULT_PARAM_TYPE],
        ], $query->params);

        $q = $query->params([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bar', 'value' => 'baz', 'type' => Dbh::PARAM_STR],
            ['name' => ':bla', 'value' => 42, 'type' => Dbh::PARAM_INT],
        ]);

        $this->assertEquals($q, $query);
        $this->assertEquals('select', $query->action);
        $this->assertEquals(['foo'], $query->tables);
        $this->assertEquals('id=:id', $query->where);
        $this->assertEquals([
            ['name' => ':id', 'value' => 1, 'type' => Dbh::DEFAULT_PARAM_TYPE],
            ['name' => ':bar', 'value' => 'baz', 'type' => Dbh::PARAM_STR],
            ['name' => ':bla', 'value' => 42, 'type' => Dbh::PARAM_INT],
        ], $query->params);

    }

}

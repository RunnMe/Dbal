<?php

namespace Running\tests\Dbal\Drivers\Sqlite\QueryBuilder;

use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Query;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testQuoteNameSimple()
    {
        $builder = (new Driver())->getQueryBuilder();

        $this->assertEquals('*', $builder->quoteName('*'));
        $this->assertEquals('func()', $builder->quoteName('func()'));
        $this->assertEquals('select some from table', $builder->quoteName('select some from table'));
        $this->assertEquals('`t1`', $builder->quoteName('t1'));
        $this->assertEquals('`j1`', $builder->quoteName('j1'));
        $this->assertEquals('`foo`', $builder->quoteName('foo'));
    }

    public function testQuoteNameComplex()
    {
        $builder = (new Driver())->getQueryBuilder();

        $this->assertEquals('`foo`.*', $builder->quoteName('foo.*'));
        $this->assertEquals('t1.`foo`', $builder->quoteName('t1.foo'));
        $this->assertEquals('j1.`foo`', $builder->quoteName('j1.foo'));
        $this->assertEquals('`foo`.`bar`', $builder->quoteName('foo.bar'));
    }

    public function testMysqlMakeStringQuery()
    {
        $builder = (new Driver())->getQueryBuilder();
        $query = new Query('SELECT * FROM `foo` WHERE `bar`=42');

        $this->assertEquals('SELECT * FROM `foo` WHERE `bar`=42', $query->string);
        $this->assertEquals('SELECT * FROM `foo` WHERE `bar`=42', $builder->makeQueryString($query));
    }

    public function testMysqlMakeSelectQuery()
    {
        $builder = (new Driver())->getQueryBuilder();
        $query = new Query();
        $query = $query->select()->from('test');

        $this->assertEquals("SELECT *\nFROM `test` AS t1", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query->select('t1.a1, t2.a2')->from('test1', 'test2')->where('a1=:a1');
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query
            ->select('t1.a1, t2.a2')
            ->from('test1', 'test2')
            ->where('a1=:a1')
            ->order('id')
            ->offset(20)
            ->limit(10);
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1\nORDER BY id\nLIMIT 10\nOFFSET 20", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query
            ->select('t1.a1, t2.a2')
            ->from('test1', 'test2')
            ->where('a1=:a1')
            ->group('a2, a3')
            ->having('a3>0')
            ->order('id')
            ->offset(20)
            ->limit(10);
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1\nGROUP BY a2, a3\nHAVING a3>0\nORDER BY id\nLIMIT 10\nOFFSET 20", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query
            ->select('t1.a1, j1.a2')
            ->from('test1, test2')
            ->join('foo', 'j1.id=t1.id')
            ->join('bar', 'j2.id<t2.id', 'left')
            ->join('baz', 'j3.id>:baz', 'cross', 'bzz');
        $this->assertEquals("SELECT t1.`a1`, j1.`a2`\nFROM `test1` AS t1, `test2` AS t2\nINNER JOIN `foo` AS j1 ON j1.id=t1.id\nLEFT JOIN `bar` AS j2 ON j2.id<t2.id\nCROSS JOIN `baz` AS `bzz` ON j3.id>:baz", $builder->makeQueryString($query));

        $driver = new Driver();
        $query = new Query();
        $query = $query->with('one AS ( SELECT 1 )')->select()->from('one');
        $this->assertEquals("WITH one AS ( SELECT 1 )\nSELECT *\nFROM `one` AS t1", $builder->makeQueryString($query));
    }

    public function testMysqlMakeInsertQuery()
    {
        $builder = (new Driver())->getQueryBuilder();

        $query = new Query();
        $query = $query->insert()->table('test')->values(['foo' => ':foo', 'bar' => ':bar']);
        $this->assertEquals("INSERT INTO `test`\n(`foo`, `bar`)\nVALUES (:foo, :bar)", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query->insert('test')->values(['foo' => ':foo', 'bar' => ':bar']);
        $this->assertEquals("INSERT INTO `test`\n(`foo`, `bar`)\nVALUES (:foo, :bar)", $builder->makeQueryString($query));
    }

    public function testMysqlMakeUpdateQuery()
    {
        $builder = (new Driver())->getQueryBuilder();

        $query = new Query();
        $query = $query->update()->table('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123');
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query->update('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123');
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query->update('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123')->order('id DESC')->limit(1);
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123\nORDER BY id DESC\nLIMIT 1", $builder->makeQueryString($query));
    }

    public function testMysqlMakeDeleteQuery()
    {
        $builder = (new Driver())->getQueryBuilder();

        $query = new Query();
        $query = $query->delete('test1, test2')->where('foo=:foo');
        $this->assertEquals("DELETE FROM `test1`, `test2`\nWHERE foo=:foo", $builder->makeQueryString($query));

        $query = new Query();
        $query = $query->delete()->from('test1, test2')->where('foo=:foo');
        $this->assertEquals("DELETE FROM `test1`, `test2`\nWHERE foo=:foo", $builder->makeQueryString($query));
    }

}
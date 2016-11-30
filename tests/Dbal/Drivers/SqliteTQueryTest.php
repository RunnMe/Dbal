<?php

namespace Running\tests\Dbal\Drivers\SqliteTQuery;

use Running\Dbal\Drivers\Sqlite;
use Running\Dbal\Query;

class SqliteTQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testMysqlMakeStringQuery()
    {
        $driver = new Sqlite();
        $query = new Query('SELECT * FROM `foo` WHERE `bar`=42');

        $this->assertEquals('SELECT * FROM `foo` WHERE `bar`=42', $query->string);
        $this->assertEquals('SELECT * FROM `foo` WHERE `bar`=42', $driver->makeQueryString($query));
    }

    public function testMysqlMakeSelectQuery()
    {
        $driver = new Sqlite();
        $query = new Query();
        $query = $query->select()->from('test');

        $this->assertEquals("SELECT *\nFROM `test` AS t1", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query->select('t1.a1, t2.a2')->from('test1', 'test2')->where('a1=:a1');
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query
            ->select('t1.a1, t2.a2')
            ->from('test1', 'test2')
            ->where('a1=:a1')
            ->order('id')
            ->offset(20)
            ->limit(10);
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1\nORDER BY id\nLIMIT 10\nOFFSET 20", $driver->makeQueryString($query));

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
        $this->assertEquals("SELECT t1.`a1`, t2.`a2`\nFROM `test1` AS t1, `test2` AS t2\nWHERE a1=:a1\nGROUP BY a2, a3\nHAVING a3>0\nORDER BY id\nLIMIT 10\nOFFSET 20", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query
            ->select('t1.a1, j1.a2')
            ->from('test1, test2')
            ->join('foo', 'j1.id=t1.id')
            ->join('bar', 'j2.id<t2.id', 'left')
            ->join('baz', 'j3.id>:baz', 'cross', 'bzz');
        $this->assertEquals("SELECT t1.`a1`, j1.`a2`\nFROM `test1` AS t1, `test2` AS t2\nINNER JOIN `foo` AS j1 ON j1.id=t1.id\nLEFT JOIN `bar` AS j2 ON j2.id<t2.id\nCROSS JOIN `baz` AS `bzz` ON j3.id>:baz", $driver->makeQueryString($query));

        $driver = new Sqlite();
        $query = new Query();
        $query = $query->with('one AS ( SELECT 1 )')->select()->from('one');
        $this->assertEquals("WITH one AS ( SELECT 1 )\nSELECT *\nFROM `one` AS t1", $driver->makeQueryString($query));
    }

    public function testMysqlMakeInsertQuery()
    {
        $driver = new Sqlite();

        $query = new Query();
        $query = $query->insert()->table('test')->values(['foo' => ':foo', 'bar' => ':bar']);
        $this->assertEquals("INSERT INTO `test`\n(`foo`, `bar`)\nVALUES (:foo, :bar)", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query->insert('test')->values(['foo' => ':foo', 'bar' => ':bar']);
        $this->assertEquals("INSERT INTO `test`\n(`foo`, `bar`)\nVALUES (:foo, :bar)", $driver->makeQueryString($query));
    }

    public function testMysqlMakeUpdateQuery()
    {
        $driver = new Sqlite();

        $query = new Query();
        $query = $query->update()->table('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123');
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query->update('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123');
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query->update('test')->values(['foo' => ':foo', 'bar' => ':bar'])->where('id=123')->order('id DESC')->limit(1);
        $this->assertEquals("UPDATE `test`\nSET `foo`=:foo, `bar`=:bar\nWHERE id=123\nORDER BY id DESC\nLIMIT 1", $driver->makeQueryString($query));
    }

    public function testMysqlMakeDeleteQuery()
    {
        $driver = new Sqlite();

        $query = new Query();
        $query = $query->delete('test1, test2')->where('foo=:foo');
        $this->assertEquals("DELETE FROM `test1`, `test2`\nWHERE foo=:foo", $driver->makeQueryString($query));

        $query = new Query();
        $query = $query->delete()->from('test1, test2')->where('foo=:foo');
        $this->assertEquals("DELETE FROM `test1`, `test2`\nWHERE foo=:foo", $driver->makeQueryString($query));
    }

}
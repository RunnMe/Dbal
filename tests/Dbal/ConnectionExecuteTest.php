<?php

namespace Runn\tests\Dbal\Connection;

use Runn\Core\Config;
use Runn\Dbal\Connection;
use Runn\Dbal\Query;

class ConnectionExecuteTest extends \PHPUnit_Framework_TestCase
{

    public function testParamsInQuery()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');

        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->into('testtable1')->insert(['foo' => ':foo', 'bar' => ':bar'])->params([':foo' => 42, ':bar' => 'test']);
        $conn->execute($query);

        $sth = $dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(42, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);

        @unlink($filename);
    }

    public function testParamsOutQuery()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');

        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->into('testtable1')->insert(['foo' => ':foo', 'bar' => ':bar']);
        $conn->execute($query, [':foo' => 42, ':bar' => 'test']);

        $sth = $dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(42, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);

        @unlink($filename);
    }

    public function testParamsMerge()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');

        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->into('testtable1')->insert(['foo' => ':foo', 'bar' => ':bar'])->params([':foo' => 42, ':bar' => 'test']);
        $conn->execute($query, [':foo' => 13]);

        $sth = $dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(13, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);

        @unlink($filename);
    }

}
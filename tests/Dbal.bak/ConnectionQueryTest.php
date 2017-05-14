<?php

namespace Running\tests\Dbal\Connection;

use Running\Core\Config;
use Running\Dbal\Connection;
use Running\Dbal\Query;

class ConnectionQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testParamsInQuery()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (1, \'test1\')');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (2, \'test2\')');

        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params([':foo' => 1]);
        $data = $conn->query($query)->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['foo']);
        $this->assertEquals('test1', $data[0]['bar']);

        @unlink($filename);
    }

    public function testParamsOutQuery()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (1, \'test1\')');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (2, \'test2\')');

        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params();
        $data = $conn->query($query, [':foo' => 1])->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['foo']);
        $this->assertEquals('test1', $data[0]['bar']);

        @unlink($filename);
    }

    public function testParamsMerge()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (1, \'test1\')');
        $dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (2, \'test2\')');

        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params([':foo' => 1]);
        $data = $conn->query($query, [':foo' => 2])->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(2, $data[0]['foo']);
        $this->assertEquals('test2', $data[0]['bar']);

        @unlink($filename);
    }

}
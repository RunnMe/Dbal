<?php

namespace Runn\tests\Dbal\Connection;

use PHPUnit\Framework\TestCase;
use Runn\Core\Config;
use Runn\Dbal\Connection;
use Runn\Dbal\Dbh;
use Runn\Dbal\Exception;
use Runn\Dbal\Query;

class ConnectionExecuteTest extends TestCase
{

    protected $filename;
    protected $dbh;

    protected function setUp(): void
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $this->dbh = new \PDO('sqlite:' . $this->filename);
        $this->dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');
    }

    protected function tearDown(): void
    {
        @unlink($this->filename);
    }

    protected function getDbConfig()
    {
        return new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => $this->filename]);
    }


    public function testInvalidPrepare()
    {
        $conn = new Connection($this->getDbConfig());

        $query = new Query('INVALIDQUERY');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQLSTATE[HY000]: General error: 1 near "INVALIDQUERY": syntax error');
        $conn->execute($query);
    }

    public function testInvalidExecuteInDefaultMode()
    {
        $config = $this->getDbConfig();
        $conn = new Connection($config);

        $query = new Query('INSERT INTO testtable1 (foo, bar) VALUES (:foo, :bar)');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQLSTATE[HY000]: General error: 25 column index out of range');
        $conn->execute($query, [':foo' => 1, ':bar' => 2, ':baz' => 3]);
    }

    public function testInvalidExecuteInSilentMode()
    {
        $config = $this->getDbConfig();
        $config->errmode = Dbh::ERRMODE_SILENT;
        $conn = new Connection($config);

        $query = new Query('INSERT INTO testtable1 (foo, bar) VALUES (:foo, :bar)');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database server cannot successfully execute the prepared statement');
        $conn->execute($query, [':foo' => 1, ':bar' => 2, ':baz' => 3]);
    }

    public function testParamsInQuery()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)
            ->into('testtable1')
            ->insert(['foo' => ':foo', 'bar' => ':bar'])
            ->params([':foo' => 42, ':bar' => 'test']);
        $conn->execute($query);

        $sth = $this->dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(42, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);
    }

    public function testParamsOutsideQuery()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)
            ->into('testtable1')
            ->insert(['foo' => ':foo', 'bar' => ':bar']);
        $conn->execute($query, [':foo' => 42, ':bar' => 'test']);

        $sth = $this->dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(42, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);
    }

    public function testParamsMerge()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)
            ->into('testtable1')
            ->insert(['foo' => ':foo', 'bar' => ':bar'])
            ->params([':foo' => 42, ':bar' => 'test']);
        $conn->execute($query, [':foo' => 13]);

        $sth = $this->dbh->query('SELECT * FROM testtable1');
        $sth->execute();
        $data = $sth->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(13, $data[0]['foo']);
        $this->assertEquals('test', $data[0]['bar']);
    }

}

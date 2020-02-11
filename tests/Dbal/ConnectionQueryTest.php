<?php

namespace Runn\tests\Dbal\Connection;

use PHPUnit\Framework\TestCase;
use Runn\Core\Config;
use Runn\Dbal\Connection;
use Runn\Dbal\Dbh;
use Runn\Dbal\Exception;
use Runn\Dbal\Query;

class ConnectionQueryTest extends TestCase
{

    protected $filename;
    protected $dbh;

    protected function setUp(): void
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $this->dbh = new \PDO('sqlite:' . $this->filename);
        $this->dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');
        $this->dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (1, \'test1\')');
        $this->dbh->exec('INSERT INTO testtable1 (foo, bar) VALUES (2, \'test2\')');
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

        $query = new Query('SELECT * FROM testtable1 WHERE foo=:foo');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQLSTATE[HY000]: General error: 25 column index out of range');
        $conn->query($query, [':foo' => 1, ':bar' => 2]);
    }

    public function testInvalidExecuteInSilentMode()
    {
        $config = $this->getDbConfig();
        $config->errmode = Dbh::ERRMODE_SILENT;
        $conn = new Connection($config);

        $query = new Query('SELECT * FROM testtable1 WHERE foo=:foo');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database server cannot successfully execute the prepared statement');
        $conn->query($query, [':foo' => 1, ':bar' => 2]);
    }

    public function testParamsInQuery()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params([':foo' => 1]);
        $data = $conn->query($query)->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['foo']);
        $this->assertEquals('test1', $data[0]['bar']);
    }

    public function testParamsOutsideQuery()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params();
        $data = $conn->query($query, [':foo' => 1])->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0]['foo']);
        $this->assertEquals('test1', $data[0]['bar']);
    }

    public function testParamsMerge()
    {
        $conn = new Connection($this->getDbConfig());

        $query = (new Query)->select()->from('testtable1')->where('foo=:foo')->params([':foo' => 1]);
        $data = $conn->query($query, [':foo' => 2])->fetchAll();

        $this->assertCount(1, $data);
        $this->assertEquals(2, $data[0]['foo']);
        $this->assertEquals('test2', $data[0]['bar']);
    }

}

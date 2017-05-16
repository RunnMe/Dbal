<?php

namespace Runn\tests\Dbal\Connection;

use Runn\Core\Config;
use Runn\Dbal\Connection;
use Runn\Dbal\Dbh;
use Runn\Dbal\DriverInterface;
use Runn\Dbal\Drivers;
use Runn\Dbal\Exception;
use Runn\Dbal\DriverBuilderInterface;
use Runn\Dbal\Query;
use Runn\Dbal\Statement;

class testStatement extends Statement {}

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    protected function methodGetDbhByConfig()
    {
        $method = new \ReflectionMethod(Connection::class, 'getDbhByConfig');
        return $method->getClosure((new \ReflectionClass(Connection::class))->newInstanceWithoutConstructor());
    }

    /**
     * @expectedException \TypeError
     */
    public function testDbhByConfigEmptyArgument()
    {
        $method = $this->methodGetDbhByConfig();
        $method();
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Can not suggest DSN class name
     */
    public function testDbhByConfigEmptyDriver()
    {
        $method = $this->methodGetDbhByConfig();
        $method(new Config());
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Can not suggest DSN class name
     */
    public function testDbhByConfigInvalidDriver()
    {
        $method = $this->methodGetDbhByConfig();
        $method(new Config(['driver' => 'invalid']));
    }

    public function testDbhByConfig()
    {
        $method = $this->methodGetDbhByConfig();

        $dbh = $method(new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_EXCEPTION, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([Statement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));

        $dbh = $method(new Config([
            'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class,
            'file' => ':memory:',
            'errmode' => Dbh::ERRMODE_SILENT,
            'statement' => testStatement::class
        ]));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_SILENT, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([testStatement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));
    }

    public function testDbhOptions()
    {
        $method = $this->methodGetDbhByConfig();
        $dbh = $method(new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:', 'options' => [Dbh::ATTR_CASE => Dbh::CASE_UPPER]]));
        $this->assertEquals(Dbh::CASE_UPPER, $dbh->getAttribute(Dbh::ATTR_CASE));
    }

    public function testDbhException()
    {
        try {
            $method = $this->methodGetDbhByConfig();
            $dbh = $method(new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => '/Invalid/File/Name']));
            $this->fail();
        } catch (Exception $e) {
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious());
            return;
        }
        $this->fail();
    }

    public function testConstruct()
    {
        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $reflectConfig = new \ReflectionProperty($conn, 'config');
        $reflectConfig->setAccessible(true);

        $reflectDbh = new \ReflectionProperty($conn, 'dbh');
        $reflectDbh->setAccessible(true);

        $reflectDriver = new \ReflectionProperty($conn, 'driver');
        $reflectDriver->setAccessible(true);

        $this->assertEquals($config, $reflectConfig->getValue($conn));
        $this->assertEquals(new Dbh('sqlite::memory:'), $reflectDbh->getValue($conn));
        $this->assertEquals(new Drivers\Sqlite\Driver(), $reflectDriver->getValue($conn));
    }

    public function testGetDbh()
    {
        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(Dbh::class, $conn->getDbh());
        $this->assertEquals(new Dbh('sqlite::memory:'), $conn->getDbh());
    }

    public function testGetDriver()
    {
        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(DriverInterface::class, $conn->getDriver());
        $this->assertInstanceOf(Drivers\Sqlite\Driver::class, $conn->getDriver());
    }

    public function testQuote()
    {
        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertEquals('\'"foo"\'', $conn->quote('"foo"'));
        $this->assertEquals('\'42\'',    $conn->quote(42));
        $this->assertEquals('\'42\'',    $conn->quote(42, Dbh::PARAM_INT));
        $this->assertEquals('\'\'',      $conn->quote(null, Dbh::PARAM_NULL));
    }

    /**
     * @expectedException \Runn\Core\Exception
     */
    public function testPrepareInvalidQuery()
    {
        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);
        $query = new Query('INVALIDQUERY');

        $sth = $conn->prepare($query);
    }

    public function testPrepare()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');

        $config = new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);
        $query = (new Query)->select('*')->from('testtable1');

        $sth = $conn->prepare($query);

        $this->assertInstanceOf(Statement::class, $sth);
        $this->assertEquals("SELECT *\nFROM `testtable1` AS t1", $sth->queryString);

        @unlink($filename);
    }

    public function testGetErrorInfo()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        try {
            $conn->prepare(new Query('INVALIDQUERY'));
        } catch (Exception $e) {
            $this->assertEquals(['HY000', 1, 'near "INVALIDQUERY": syntax error'], $conn->getErrorInfo());
        }
    }

    public function testSleep()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $this->assertEquals(['config'], $conn->__sleep());
    }

    public function testWakeUp()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $connReflection = new \ReflectionClass($conn);
        $property = $connReflection->getProperty('dbh');
        $property->setAccessible(true);
        $property->setValue($conn, null);
        $property = $connReflection->getProperty('driver');
        $property->setAccessible(true);
        $property->setValue($conn, null);
        $conn->__wakeup();
        $this->assertInstanceOf(Dbh::class, $conn->getDbh());
        $this->assertInstanceOf(DriverInterface::class, $conn->getDriver());
    }

    public function testLastInsertId()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $conn->execute(new Query('CREATE TABLE testtable1 (foo INT, bar TEXT)'));
        $query = (new Query)->into('testtable1')->insert(['foo' => 1, 'bar' => '1']);
        $conn->prepare($query)->execute();
        $this->assertEquals(1, $conn->lastInsertId());
    }

    public function testTransactionBegin()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $this->assertTrue($conn->transactionBegin());
        try {
            $conn->transactionBegin();
            $this->fail();
        } catch (\PDOException $e) {
            $this->assertEquals('There is already an active transaction', $e->getMessage());
        }
    }

    public function testTransactionCommit()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $conn->execute(new Query('CREATE TABLE testtable1 (foo INT, bar TEXT)'));
        $query = (new Query)->select('COUNT(*)')->from('testtable1');
        $sth = $conn->prepare($query);
        $sth->execute();
        $this->assertEquals('0', $sth->fetchScalar());
        $this->assertTrue($conn->transactionBegin());
        $query = (new Query)->into('testtable1')->insert(['foo' => 1, 'bar' => '1']);
        $conn->prepare($query)->execute();
        $query = (new Query)->into('testtable1')->insert(['foo' => 2, 'bar' => '2']);
        $conn->prepare($query)->execute();
        $this->assertTrue($conn->transactionCommit());
        $sth->execute();
        $this->assertEquals('2', $sth->fetchScalar());
        try {
            $conn->transactionCommit();
            $this->fail();
        } catch (\PDOException $e) {
            $this->assertEquals('There is no active transaction', $e->getMessage());
        }
    }

    public function testTransactionRollback()
    {
        $conn = new Connection(
            new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $conn->execute(new Query('CREATE TABLE testtable1 (foo INT, bar TEXT)'));
        $query = (new Query)->select('COUNT(*)')->from('testtable1');
        $sth = $conn->prepare($query);
        $sth->execute();
        $this->assertEquals('0', $sth->fetchScalar());
        $this->assertTrue($conn->transactionBegin());
        $query = (new Query)->into('testtable1')->insert(['foo' => 1, 'bar' => '1']);
        $conn->prepare($query)->execute();
        $query = (new Query)->into('testtable1')->insert(['foo' => 2, 'bar' => '2']);
        $conn->prepare($query)->execute();
        $this->assertTrue($conn->transactionRollback());
        $sth->execute();
        $this->assertEquals('0', $sth->fetchScalar());
        try {
            $conn->transactionRollback();
            $this->fail();
        } catch (\PDOException $e) {
            $this->assertEquals('There is no active transaction', $e->getMessage());
        }
    }
}

<?php

namespace Running\tests\Dbal\Connection;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Connection;
use Running\Dbal\Dbh;
use Running\Dbal\DriverInterface;
use Running\Dbal\Drivers;
use Running\Dbal\Exception;
use Running\Dbal\DriverBuilderInterface;
use Running\Dbal\Query;
use Running\Dbal\Statement;

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

    public function testDbhByConfigEmptyDriver()
    {
        try {
            $method = $this->methodGetDbhByConfig();
            $method(new Config());
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class,               $errors[0]);
            $this->assertEquals('Can not suggest DSN class name',   $errors[0]->getMessage());
        }
    }

    public function testDbhByConfigInvalidDriver()
    {
        try {
            $method = $this->methodGetDbhByConfig();
            $method(new Config(['driver' => 'invalid']));
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class,               $errors[0]);
            $this->assertEquals('Can not suggest DSN class name',   $errors[0]->getMessage());
        }
    }

    public function testDbhByConfig()
    {
        $method = $this->methodGetDbhByConfig();

        $dbh = $method(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_EXCEPTION, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([Statement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));

        $dbh = $method(new Config([
            'driver' => \Running\Dbal\Drivers\Sqlite\Driver::class,
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
        $dbh = $method(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:', 'options' => [Dbh::ATTR_CASE => Dbh::CASE_UPPER]]));
        $this->assertEquals(Dbh::CASE_UPPER, $dbh->getAttribute(Dbh::ATTR_CASE));
    }

    public function testDbhException()
    {
        try {
            $method = $this->methodGetDbhByConfig();
            $dbh = $method(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => '/Invalid/File/Name']));
            $this->fail();
        } catch (Exception $e) {
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious());
            return;
        }
        $this->fail();
    }

    public function testConstruct()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
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

    public function testGetConfig()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(Config::class, $conn->getConfig());
        $this->assertEquals($config, $conn->getConfig());
    }

    public function testGetDbh()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(Dbh::class, $conn->getDbh());
        $this->assertEquals(new Dbh('sqlite::memory:'), $conn->getDbh());
    }

    public function testGetDriver()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(DriverInterface::class, $conn->getDriver());
        $this->assertInstanceOf(Drivers\Sqlite\Driver::class, $conn->getDriver());
    }

    public function testQuote()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertEquals('\'"foo"\'', $conn->quote('"foo"'));
        $this->assertEquals('\'42\'',    $conn->quote(42));
        $this->assertEquals('\'42\'',    $conn->quote(42, Dbh::PARAM_INT));
        $this->assertEquals('\'\'',      $conn->quote(null, Dbh::PARAM_NULL));
    }

    /**
     * @expectedException \Running\Core\Exception
     */
    public function testPrepareInvalidQuery()
    {
        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']);
        $conn = new Connection($config);
        $query = new Query('INVALIDQUERY');

        $sth = $conn->prepare($query);
    }

    public function testPrepare()
    {
        $filename = tempnam(sys_get_temp_dir(), 'SqliteTest');
        $dbh = new \PDO('sqlite:' . $filename);
        $dbh->exec('CREATE TABLE testtable1 (foo INT, bar TEXT)');

        $config = new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => $filename]);
        $conn = new Connection($config);
        $query = (new Query)->select('*')->from('testtable1');

        $sth = $conn->prepare($query);

        $this->assertInstanceOf(Statement::class, $sth);
        $this->assertEquals("SELECT *\nFROM `testtable1` AS t1", $sth->queryString);

        unlink($filename);
    }

}
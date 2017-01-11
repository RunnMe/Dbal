<?php

namespace Running\tests\Dbal\Connection;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Connection;
use Running\Dbal\Dbh;
use Running\Dbal\Exception;
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
            $this->assertInstanceOf(Exception::class,           $errors[0]);
            $this->assertEquals('Driver is empty in config',    $errors[0]->getMessage());
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
            $this->assertInstanceOf(Exception::class,           $errors[0]);
            $this->assertEquals('Driver is invalid',            $errors[0]->getMessage());
        }
    }

    public function testDbhByConfig()
    {
        $method = $this->methodGetDbhByConfig();

        $dbh = $method(new Config(['driver' => 'sqlite', 'file' => ':memory:']));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_EXCEPTION, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([Statement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));

        $dbh = $method(new Config([
            'driver' => 'sqlite',
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
        $dbh = $method(new Config(['driver' => 'sqlite', 'file' => ':memory:', 'options' => [Dbh::ATTR_CASE => Dbh::CASE_UPPER]]));
        $this->assertEquals(Dbh::CASE_UPPER, $dbh->getAttribute(Dbh::ATTR_CASE));
    }

    /*

    public function testPdoException()
    {
        try {
            $pdo = $this->methodGetPdoByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'invalid', 'user' => 'invalid', 'password' => 'invalid']));
            $this->fail();
        } catch (Exception $e) {
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious());
        }
    }

    public function testConstruct()
    {
        $config = new Config(['driver' => 'sqlite', 'file' => ':memory:']);
        $conn = new Connection($config);

        $reflectConfig = new \ReflectionProperty($conn, 'config');
        $reflectConfig->setAccessible(true);

        $reflectPdo = new \ReflectionProperty($conn, 'pdo');
        $reflectPdo->setAccessible(true);

        $this->assertEquals($config, $reflectConfig->getValue($conn));
        $this->assertEquals(new \PDO('sqlite::memory:'), $reflectPdo->getValue($conn));
    }

    public function testQuote()
    {
        $config = new Config(['driver' => 'sqlite', 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertEquals('\'"foo"\'', $conn->quote('"foo"'));
        $this->assertEquals('\'42\'', $conn->quote(42));
        $this->assertEquals('\'42\'', $conn->quote(42, \PDO::PARAM_INT));
    }

    public function testGetDriver()
    {
        $config = new Config(['driver' => 'sqlite', 'file' => ':memory:']);
        $conn = new Connection($config);

        $this->assertInstanceOf(IDriver::class, $conn->getDriver());
        $this->assertInstanceOf(Sqlite::class, $conn->getDriver());
    }
*/
}
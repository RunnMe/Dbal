<?php

namespace Running\tests\Dbal\Connection;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Connection;
use Running\Dbal\Drivers\Sqlite;
use Running\Dbal\Exception;
use Running\Dbal\IDriver;
use Running\Dbal\Statement;

class testStatement extends \PDOStatement {}

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    protected function methodGetDsnByConfig()
    {
        $method = new \ReflectionMethod(Connection::class, 'getDsnByConfig');
        return $method->getClosure((new \ReflectionClass(Connection::class))->newInstanceWithoutConstructor());
    }

    protected function methodGetPdoByConfig()
    {
        $method = new \ReflectionMethod(Connection::class, 'getPdoByConfig');
        return $method->getClosure((new \ReflectionClass(Connection::class))->newInstanceWithoutConstructor());
    }

    /**
     * @expectedException \TypeError
     */
    public function testDsnByConfigEmptyArgument()
    {
        $this->methodGetDsnByConfig()();
    }

    public function testDsnByConfigEmptyConfig()
    {
        try {
            $this->methodGetDsnByConfig()(new Config());
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class,       $errors[0]);
            $this->assertEquals('Empty driver in config',   $errors[0]->getMessage());
        }
    }

    public function testDsnByConfigEmptyRequiredConfig()
    {
        try {
            $this->methodGetDsnByConfig()(new Config(['driver' => 'mysql']));
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(2, $errors);
            $this->assertInstanceOf(Exception::class,       $errors[0]);
            $this->assertEquals('Empty host in config',     $errors[0]->getMessage());
            $this->assertInstanceOf(Exception::class,       $errors[1]);
            $this->assertEquals('Empty dbname in config',   $errors[1]->getMessage());
        }
    }

    public function testDsnByConfigRequired()
    {
        $dsn = $this->methodGetDsnByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'test']));
        $this->assertEquals('mysql:host=localhost;dbname=test', $dsn);
    }

    public function testDsnByConfigOptional()
    {
        $dsn = $this->methodGetDsnByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'test', 'port' => 3306]));
        $this->assertEquals('mysql:host=localhost;dbname=test;port=3306', $dsn);
    }

    public function testDsnByConfigSqlite()
    {
        $dsn = $this->methodGetDsnByConfig()(new Config(['driver' => 'sqlite', 'file' => '/tmp/test.sql']));
        $this->assertEquals('sqlite:/tmp/test.sql', $dsn);
    }

    /**
     * @expectedException \TypeError
     */
    public function testPdoByConfigEmptyArgument()
    {
        $this->methodGetPdoByConfig()();
    }

    public function testPdoByConfigEmptyConfig()
    {
        try {
            $this->methodGetPdoByConfig()(new Config());
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class,       $errors[0]);
            $this->assertEquals('Empty driver in config',   $errors[0]->getMessage());
        }
    }

    public function testPdoByConfig()
    {
        $pdo = $this->methodGetPdoByConfig()(new Config(['driver' => 'sqlite', 'file' => ':memory:']));

        $this->assertInstanceOf(\PDO::class, $pdo);
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
        $this->assertEquals([Statement::class], $pdo->getAttribute(\PDO::ATTR_STATEMENT_CLASS));

        $pdo = $this->methodGetPdoByConfig()(new Config(['driver' => 'sqlite', 'file' => ':memory:', 'errmode' => \PDO::ERRMODE_SILENT, 'statement' => testStatement::class]));

        $this->assertInstanceOf(\PDO::class, $pdo);
        $this->assertEquals(\PDO::ERRMODE_SILENT, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
        $this->assertEquals([testStatement::class], $pdo->getAttribute(\PDO::ATTR_STATEMENT_CLASS));
    }

    public function testPdoException()
    {
        try {
            $pdo = $this->methodGetPdoByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'invalid', 'user' => 'invalid', 'password' => 'invalid']));
            $this->fail();
        } catch (Exception $e) {
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious());
        }
    }

    public function testPdoOptions()
    {
        $pdo = $this->methodGetPdoByConfig()(new Config(['driver' => 'sqlite', 'file' => ':memory:', 'options' => [\PDO::ATTR_CASE => \PDO::CASE_UPPER]]));

        $this->assertInstanceOf(\PDO::class, $pdo);
        $this->assertEquals(\PDO::CASE_UPPER, $pdo->getAttribute(\PDO::ATTR_CASE));
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

}
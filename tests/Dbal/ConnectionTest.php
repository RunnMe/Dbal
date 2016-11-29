<?php

namespace Running\tests\Dbal\Statement;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Connection;
use Running\Dbal\Exception;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    protected function methodGetDsnByConfig()
    {
        $method = new \ReflectionMethod(Connection::class, 'getDsnByConfig');
        return $method->getClosure((new \ReflectionClass(Connection::class))->newInstanceWithoutConstructor());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage Argument 1 passed to Running\Dbal\Connection::getDsnByConfig() must be an instance of Running\Core\Config, none given
     */
    public function testDsnByConfigEmptyArgument()
    {
        $this->methodGetDsnByConfig()();
    }

    public function testDsnByConfigEmptyDriverHostDbname()
    {
        try {
            $this->methodGetDsnByConfig()(new Config());
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(3, $errors);
            $this->assertInstanceOf(Exception::class,       $errors[0]);
            $this->assertEquals('Empty driver in config',   $errors[0]->getMessage());
            $this->assertInstanceOf(Exception::class,       $errors[1]);
            $this->assertEquals('Empty host in config',     $errors[1]->getMessage());
            $this->assertInstanceOf(Exception::class,       $errors[2]);
            $this->assertEquals('Empty dbname in config',   $errors[2]->getMessage());
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

    public function testDsnByConfigWoPort()
    {
        $dsn = $this->methodGetDsnByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'test']));
        $this->assertEquals('mysql:host=localhost;dbname=test', $dsn);
    }

    public function testDsnByConfigWPort()
    {
        $dsn = $this->methodGetDsnByConfig()(new Config(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'test', 'port' => 3306]));
        $this->assertEquals('mysql:host=localhost;dbname=test;port=3306', $dsn);
    }

}
<?php

namespace Running\tests\Dbal\Drivers\Mysql\Dsn;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Exception;
use Running\Dbal\Dsn;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testWithNoRequired()
    {
        try {
            $dsn = Dsn::instance(
                new Config(['driver' => 'mysql', 'foo' => 'bar', 'baz' => 42])
            );
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class, $errors[0]);
            $this->assertEquals('"host" is not set in config', $errors[0]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testToString()
    {
        $config = new Config([
            'driver' => 'mysql', 'host' => 'foo', 'dbname' => 'baz', 'port' => 1234, 'charset' => 'utf8', 'foo' => 'baz'
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('mysql:host=foo;port=1234;dbname=baz;charset=utf8', (string)$dsn);
    }
}

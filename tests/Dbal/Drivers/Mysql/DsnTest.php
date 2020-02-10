<?php

namespace Runn\tests\Dbal\Drivers\Mysql\Dsn;

use PHPUnit\Framework\TestCase;
use Runn\Core\Config;
use Runn\Core\Exceptions;
use Runn\Dbal\Exception;
use Runn\Dbal\Dsn;

class DsnTest extends TestCase
{

    public function testWithNoRequired()
    {
        try {
            $dsn = Dsn::instance(
                new Config(['class' => \Runn\Dbal\Drivers\Mysql\Dsn::class, 'foo' => 'bar', 'baz' => 42])
            );
        } catch (Exceptions $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class, $errors[0]);
            $this->assertEquals('Attribute "host" is not set in DSN config', $errors[0]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testToString()
    {
        $config = new Config([
            'class' => \Runn\Dbal\Drivers\Mysql\Dsn::class, 'host' => 'foo', 'dbname' => 'baz', 'port' => 1234, 'charset' => 'utf8', 'foo' => 'baz'
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('mysql:host=foo;port=1234;dbname=baz;charset=utf8', (string)$dsn);
    }

}

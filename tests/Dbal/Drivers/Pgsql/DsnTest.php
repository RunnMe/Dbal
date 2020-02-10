<?php

namespace Runn\tests\Dbal\Drivers\Pgsql\Dsn;

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
                new Config(['class' => \Runn\Dbal\Drivers\Pgsql\Dsn::class, 'foo' => 'bar', 'baz' => 42])
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
            'class' => \Runn\Dbal\Drivers\Pgsql\Dsn::class, 'host' => 'foo', 'dbname' => 'bar', 'port' => 1234
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('pgsql:host=foo;port=1234;dbname=bar', (string)$dsn);

        $config = new Config([
            'class' => \Runn\Dbal\Drivers\Pgsql\Dsn::class, 'host' => 'foo', 'dbname' => 'baz', 'user' => 'postgres', 'password' => 'somepass'
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('pgsql:host=foo;dbname=baz;user=postgres;password=somepass', (string)$dsn);
    }

}

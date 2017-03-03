<?php

namespace Running\tests\Dbal\Drivers\Pgsql\Dsn;

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
                new Config(['class' => \Running\Dbal\Drivers\Pgsql\Dsn::class, 'foo' => 'bar', 'baz' => 42])
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
            'class' => \Running\Dbal\Drivers\Pgsql\Dsn::class, 'host' => 'foo', 'dbname' => 'bar', 'port' => 1234
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('pgsql:host=foo;port=1234;dbname=bar', (string)$dsn);

        $config = new Config([
            'class' => \Running\Dbal\Drivers\Pgsql\Dsn::class, 'host' => 'foo', 'dbname' => 'baz', 'user' => 'postgres', 'password' => 'somepass'
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('pgsql:host=foo;dbname=baz;user=postgres;password=somepass', (string)$dsn);
    }
}

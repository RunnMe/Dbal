<?php

namespace Running\tests\Dbal\Drivers\Sqlite;

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
                new Config(['class' => \Running\Dbal\Drivers\Sqlite\Dsn::class, 'foo' => 'bar', 'baz' => 42])
            );
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class, $errors[0]);
            $this->assertEquals('"file" is not set in config', $errors[0]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testToString()
    {
        $config = new Config(['class' => \Running\Dbal\Drivers\Sqlite\Dsn::class, 'file' => '/tmp/test.sqlite', 'foo' => 'baz']);
        $dsn = Dsn::instance($config);
        $this->assertEquals('sqlite:/tmp/test.sqlite', (string)$dsn);

        $config = new Config(['class' => \Running\Dbal\Drivers\Sqlite\Dsn::class, 'file' => ':memory:', 'foo' => 'baz']);
        $dsn = Dsn::instance($config);
        $this->assertEquals('sqlite::memory:', (string)$dsn);
    }
}

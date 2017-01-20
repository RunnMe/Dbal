<?php

namespace Running\Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Exception;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $config = new Config(['driver' => 'sqlite', 'foo' => 'test']);
        try {
            new Dsn($config);
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertInstanceOf(Exception::class, $errors[0]);
            $this->assertEquals('"file" is not set in config', $errors[0]->getMessage());
        }
        $config = new Config(['driver' => 'sqlite', 'file' => '/test']);
        $dsn = new Dsn($config);
        $this->assertEquals('sqlite:/test', (string)$dsn);
    }
}

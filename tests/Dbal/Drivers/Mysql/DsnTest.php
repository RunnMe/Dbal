<?php

namespace Running\Dbal\Drivers\Mysql;

use Running\Core\Config;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $config = new Config([
            'driver' => 'mysql', 'host' => 'foo', 'dbname' => 'baz', 'port' => 1123, 'charset' => 'utf8', 'foo' => 'baz'
        ]);
        $dsn = Dsn::instance($config);
        $this->assertEquals('mysql:host=foo;dbname=baz;port=1123;charset=utf8', (string)$dsn);
    }
}

<?php

namespace Running\Dbal\Drivers\Sqlite;

use Running\Core\Config;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $config = new Config(['driver' => 'sqlite', 'file' => '/test', 'foo' => 'baz']);
        $dsn = new Dsn($config);
        $this->assertEquals('sqlite:/test', (string)$dsn);
    }
}

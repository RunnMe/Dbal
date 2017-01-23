<?php

namespace Running\Dbal\Drivers\Pgsql;

use Running\Core\Config;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $config = new Config([
            'driver' => 'pgsql', 'host' => 'foo', 'dbname' => 'baz', 'port' => 1123, 'foo' => 'baz'
        ]);
        $dsn = new Dsn($config);
        $this->assertEquals('pgsql:host=foo;dbname=baz;port=1123', (string)$dsn);
    }
}

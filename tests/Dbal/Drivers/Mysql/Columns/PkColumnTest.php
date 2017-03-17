<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\PkColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class PkColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('SERIAL', $driver->getColumnDDL(new PkColumn()));

        $driver = new Driver();
        $this->assertSame('SERIAL', $driver->getColumnDDL(new PkColumn(['autoincrement' => true])));

        $driver = new Driver();
        $this->assertSame('BIGINT UNSIGNED NOT NULL', $driver->getColumnDDL(new PkColumn(['autoincrement' => false])));
    }
}

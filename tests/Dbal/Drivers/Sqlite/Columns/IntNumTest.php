<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\IntNum;
use Running\Dbal\Drivers\Sqlite\Driver;

class IntNumTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new IntNum();
        $this->assertSame('INTEGER', $driver->getColumnDDL($column));

        $column = new IntNum(['default' => null]);
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new IntNum(['default' => 42]);
        $this->assertSame('INTEGER DEFAULT 42', $driver->getColumnDDL($column));
    }

}
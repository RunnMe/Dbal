<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\IntColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class IntColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new IntColumn();
        $this->assertSame('INTEGER', $driver->getColumnDDL($column));

        $column = new IntColumn(['default' => null]);
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new IntColumn(['default' => 42]);
        $this->assertSame('INTEGER DEFAULT 42', $driver->getColumnDDL($column));
    }

}
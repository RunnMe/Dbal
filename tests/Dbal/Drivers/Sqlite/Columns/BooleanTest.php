<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\Boolean;
use Running\Dbal\Drivers\Sqlite\Driver;

class BooleanTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new Boolean();
        $this->assertSame('INTEGER', $driver->getColumnDDL($column));

        $column = new Boolean(['default' => null]);
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new Boolean(['default' => true]);
        $this->assertSame('INTEGER DEFAULT 1', $driver->getColumnDDL($column));
    }

}
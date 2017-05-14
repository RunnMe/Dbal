<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\TimeColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class TimeColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new TimeColumn();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new TimeColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new TimeColumn(['default' => '12:00:00']);
        $this->assertSame('TEXT DEFAULT \'12:00:00\'', $driver->getColumnDDL($column));
    }

}
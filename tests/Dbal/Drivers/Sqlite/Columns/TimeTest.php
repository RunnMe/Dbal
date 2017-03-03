<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\Time;
use Running\Dbal\Drivers\Sqlite\Driver;

class TimeTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new Time();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new Time(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new Time(['default' => '12:00:00']);
        $this->assertSame('TEXT DEFAULT \'12:00:00\'', $driver->getColumnDDL($column));
    }

}
<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\Date;
use Running\Dbal\Columns\DateTime;
use Running\Dbal\Drivers\Sqlite\Driver;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateTime();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new DateTime(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new DateTime(['default' => '2000-01-01 12:00:00']);
        $this->assertSame('TEXT DEFAULT \'2000-01-01 12:00:00\'', $driver->getColumnDDL($column));
    }

}
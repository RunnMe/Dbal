<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\Date;
use Running\Dbal\Drivers\Sqlite\Driver;

class DateTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new Date();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new Date(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new Date(['default' => '2000-01-01']);
        $this->assertSame('TEXT DEFAULT \'2000-01-01\'', $driver->getColumnDDL($column));
    }

}
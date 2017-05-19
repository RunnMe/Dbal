<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\DateColumn;
use Runn\Dbal\Columns\DateTimeColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class DateTimeColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateTimeColumn();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => '2000-01-01 12:00:00']);
        $this->assertSame('TEXT DEFAULT \'2000-01-01 12:00:00\'', $driver->getColumnDDL($column));
    }

}
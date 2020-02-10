<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\DateTimeColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class DateTimeColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateTimeColumn();
        $this->assertSame('TEXT', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => '2000-01-01 12:00:00']);
        $this->assertSame('TEXT DEFAULT \'2000-01-01 12:00:00\'', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}

<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\DateColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class DateColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateColumn();
        $this->assertSame('TEXT', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new DateColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new DateColumn(['default' => '2000-01-01']);
        $this->assertSame('TEXT DEFAULT \'2000-01-01\'', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}

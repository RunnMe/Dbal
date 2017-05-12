<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\DateTimeColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class DateTimeColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateTimeColumn();
        $this->assertSame('DATETIME', $driver->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => null]);
        $this->assertSame('DATETIME DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => '2000-01-01 12:00:00']);
        $this->assertSame('DATETIME DEFAULT \'2000-01-01 12:00:00\'', $driver->getColumnDDL($column));

        $column = new DateTimeColumn(['default' => '2000-01-01 42:00:00']);
        $this->assertSame('DATETIME DEFAULT NULL', $driver->getColumnDDL($column));
    }
}

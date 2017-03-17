<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\DateColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class DateColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new DateColumn();
        $this->assertSame('DATE', $driver->getColumnDDL($column));

        $column = new DateColumn(['default' => null]);
        $this->assertSame('DATE DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new DateColumn(['default' => '2000-01-01']);
        $this->assertSame('DATE DEFAULT \'2000-01-01\'', $driver->getColumnDDL($column));

        $column = new DateColumn(['default' => '2000-01-41']);
        $this->assertSame('DATE DEFAULT NULL', $driver->getColumnDDL($column));
    }

}
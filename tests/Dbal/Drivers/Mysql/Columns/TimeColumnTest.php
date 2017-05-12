<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\TimeColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class TimeColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new TimeColumn();
        $this->assertSame('TIME', $driver->getColumnDDL($column));

        $column = new TimeColumn(['default' => null]);
        $this->assertSame('TIME DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new TimeColumn(['default' => '12:00:00']);
        $this->assertSame('TIME DEFAULT \'12:00:00\'', $driver->getColumnDDL($column));

        $column = new TimeColumn(['default' => '42:00:00']);
        $this->assertSame('TIME DEFAULT NULL', $driver->getColumnDDL($column));
    }
}

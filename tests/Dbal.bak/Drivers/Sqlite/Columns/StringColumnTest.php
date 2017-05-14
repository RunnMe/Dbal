<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\StringColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class StringColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new StringColumn();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new StringColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new StringColumn(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }

}
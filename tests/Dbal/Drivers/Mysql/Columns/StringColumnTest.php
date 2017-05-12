<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\StringColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class StringColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new StringColumn();
        $this->assertSame('VARCHAR(255)', $driver->getColumnDDL($column));

        $column = new StringColumn(['default' => null]);
        $this->assertSame('VARCHAR(255) DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new StringColumn(['default' => 'foo']);
        $this->assertSame('VARCHAR(255) DEFAULT \'foo\'', $driver->getColumnDDL($column));

        $column = new StringColumn(['default' => 'foo', 'length' => 42]);
        $this->assertSame('VARCHAR(42) DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }
}

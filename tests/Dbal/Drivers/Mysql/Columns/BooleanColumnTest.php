<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\BooleanColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class BooleanColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new BooleanColumn();
        $this->assertSame('BOOLEAN', $driver->getColumnDDL($column));

        $column = new BooleanColumn(['default' => null]);
        $this->assertSame('BOOLEAN DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new BooleanColumn(['default' => true]);
        $this->assertSame('BOOLEAN DEFAULT 1', $driver->getColumnDDL($column));
    }

}
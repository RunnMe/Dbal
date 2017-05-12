<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\FloatColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class FloatColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new FloatColumn();
        $this->assertSame('REAL', $driver->getColumnDDL($column));

        $column = new FloatColumn(['default' => null]);
        $this->assertSame('REAL DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new FloatColumn(['default' => 3.14]);
        $this->assertSame('REAL DEFAULT 3.14', $driver->getColumnDDL($column));

        $column = new FloatColumn(['default' => 3.14, 'dimension' => 10]);
        $this->assertSame('REAL(10) DEFAULT 3.14', $driver->getColumnDDL($column));
    }
}

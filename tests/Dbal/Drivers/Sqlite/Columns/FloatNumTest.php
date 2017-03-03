<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\FloatNum;
use Running\Dbal\Drivers\Sqlite\Driver;

class FloatNumTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new FloatNum();
        $this->assertSame('REAL', $driver->getColumnDDL($column));

        $column = new FloatNum(['default' => null]);
        $this->assertSame('REAL DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new FloatNum(['default' => 3.14]);
        $this->assertSame('REAL DEFAULT 3.14', $driver->getColumnDDL($column));
    }

}
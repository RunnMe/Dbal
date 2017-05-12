<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\CharColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class CharColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new CharColumn();
        $this->assertSame('CHAR(255)', $driver->getColumnDDL($column));

        $column = new CharColumn(['default' => null]);
        $this->assertSame('CHAR(255) DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new CharColumn(['default' => 'foo']);
        $this->assertSame('CHAR(255) DEFAULT \'foo\'', $driver->getColumnDDL($column));

        $column = new CharColumn(['default' => 'foo', 'length' => 42]);
        $this->assertSame('CHAR(42) DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }
}

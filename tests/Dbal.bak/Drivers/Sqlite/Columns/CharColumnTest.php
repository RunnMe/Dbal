<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\CharColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class CharColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new CharColumn();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new CharColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new CharColumn(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }

}
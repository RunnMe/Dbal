<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\TextColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class TextColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new TextColumn();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new TextColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new TextColumn(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }
}

<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\Varchar;
use Running\Dbal\Drivers\Sqlite\Driver;

class VarcharTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new Varchar();
        $this->assertSame('TEXT', $driver->getColumnDDL($column));

        $column = new Varchar(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new Varchar(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }

}
<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\CharColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

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
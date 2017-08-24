<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\StringColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class StringColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new StringColumn();
        $this->assertSame('TEXT', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new StringColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new StringColumn(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}
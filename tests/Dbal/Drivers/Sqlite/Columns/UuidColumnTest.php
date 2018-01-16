<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\UuidColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class UuidColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new UuidColumn();
        $this->assertSame('TEXT', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new UuidColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new UuidColumn(['default' => '00000000-0000-0000-0000-000000000000']);
        $this->assertSame('TEXT DEFAULT \'00000000-0000-0000-0000-000000000000\'', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}

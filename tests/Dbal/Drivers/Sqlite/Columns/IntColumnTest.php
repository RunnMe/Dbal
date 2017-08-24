<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\IntColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class IntColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new IntColumn();
        $this->assertSame('INTEGER', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new IntColumn(['default' => null]);
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new IntColumn(['default' => 42]);
        $this->assertSame('INTEGER DEFAULT 42', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}
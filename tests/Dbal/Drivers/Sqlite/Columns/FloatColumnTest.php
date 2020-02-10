<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\FloatColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class FloatColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new FloatColumn();
        $this->assertSame('REAL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new FloatColumn(['default' => null]);
        $this->assertSame('REAL DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new FloatColumn(['default' => 3.14]);
        $this->assertSame('REAL DEFAULT 3.14', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}

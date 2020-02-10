<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\CharColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class CharColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();

        $column = new CharColumn();
        $this->assertSame('TEXT', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new CharColumn(['default' => null]);
        $this->assertSame('TEXT DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL($column));

        $column = new CharColumn(['default' => 'foo']);
        $this->assertSame('TEXT DEFAULT \'foo\'', $driver->getQueryBuilder()->getColumnDDL($column));
    }

}

<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\PkColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class PkColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER PRIMARY KEY AUTOINCREMENT', $driver->getQueryBuilder()->getColumnDDL(new PkColumn()));
    }

}

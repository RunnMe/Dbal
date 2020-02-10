<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\SerialColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class SerialColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER AUTOINCREMENT', $driver->getQueryBuilder()->getColumnDDL(new SerialColumn()));
    }

}

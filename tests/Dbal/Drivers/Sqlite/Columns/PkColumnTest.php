<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Columns\PkColumn;
use Running\Dbal\Drivers\Sqlite\Driver;

class PkColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER PRIMARY KEY AUTOINCREMENT', $driver->getColumnDDL(new PkColumn()));
    }

}
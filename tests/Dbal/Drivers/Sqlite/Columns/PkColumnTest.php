<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\PkColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class PkColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER PRIMARY KEY AUTOINCREMENT', $driver->getColumnDDL(new PkColumn()));
    }

}
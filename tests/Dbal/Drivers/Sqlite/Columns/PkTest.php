<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use App\Dbal\Columns\Pk;
use Running\Dbal\Drivers\Sqlite\Driver;

class PkTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER PRIMARY KEY AUTOINCREMENT', $driver->getColumnDDL(new Pk()));
    }

}
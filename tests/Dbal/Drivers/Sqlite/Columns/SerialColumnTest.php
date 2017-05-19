<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\SerialColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class SerialColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER AUTOINCREMENT', $driver->getColumnDDL(new SerialColumn()));
    }

}
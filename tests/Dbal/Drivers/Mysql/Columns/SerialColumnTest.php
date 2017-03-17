<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\SerialColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class SerialColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('SERIAL', $driver->getColumnDDL(new SerialColumn()));
    }

}
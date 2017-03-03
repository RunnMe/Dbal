<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use App\Dbal\Columns\Link;
use Running\Dbal\Drivers\Sqlite\Driver;

class LinkTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER', $driver->getColumnDDL(new Link()));
    }

}
<?php

namespace Running\tests\Dbal\Drivers\Mysql\Columns;

use Running\Dbal\Columns\LinkColumn;
use Running\Dbal\Drivers\Mysql\Driver;

class LinkColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('BIGINT UNSIGNED NULL DEFAULT NULL', $driver->getColumnDDL(new LinkColumn()));
    }

}
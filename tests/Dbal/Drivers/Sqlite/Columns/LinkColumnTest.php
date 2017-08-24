<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Columns\LinkColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class LinkColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL(new LinkColumn()));
    }

}
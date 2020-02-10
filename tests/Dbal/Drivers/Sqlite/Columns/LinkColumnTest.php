<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\LinkColumn;
use Runn\Dbal\Drivers\Sqlite\Driver;

class LinkColumnTest extends TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $this->assertSame('INTEGER DEFAULT NULL', $driver->getQueryBuilder()->getColumnDDL(new LinkColumn()));
    }

}

<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

use Running\Dbal\Column;
use Running\Dbal\DriverInterface;
use Running\Dbal\Drivers\Sqlite\Driver;

class CustomTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnDDL()
    {
        $driver = new Driver();
        $column = new class extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver)
            {
                return 'CUSTOM_COLUMN';
            }
        };
        $this->assertSame('CUSTOM_COLUMN', $driver->getColumnDDL($column));
    }

}
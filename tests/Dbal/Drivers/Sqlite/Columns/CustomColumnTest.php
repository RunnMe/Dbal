<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use Runn\Dbal\Column;
use Runn\Dbal\DriverInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;

class CustomColumnTest extends \PHPUnit_Framework_TestCase
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

        $column = new class (['default' => null]) extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver)
            {
                return 'CUSTOM_COLUMN' . ( isset($this->default) ? ' DEFAULT ' . (null === $this->default ? 'NULL' : "'" . $this->default . "'") : null );
            }
        };
        $this->assertSame('CUSTOM_COLUMN DEFAULT NULL', $driver->getColumnDDL($column));

        $column = new class (['default' => 'foo']) extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver)
            {
                return 'CUSTOM_COLUMN' . ( isset($this->default) ? ' DEFAULT ' . (null === $this->default ? 'NULL' : "'" . $this->default . "'") : null );
            }
        };
        $this->assertSame('CUSTOM_COLUMN DEFAULT \'foo\'', $driver->getColumnDDL($column));
    }

}
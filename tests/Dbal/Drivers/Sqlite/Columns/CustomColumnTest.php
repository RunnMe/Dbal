<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Column;
use Runn\Dbal\DriverInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;

class CustomColumnTest extends TestCase
{

    public function testUnoverridedMethod()
    {
        $driver = new Driver();
        $column = new class extends Column {};

        $this->expectException(\BadMethodCallException::class);
        $driver->getQueryBuilder()->getColumnDDL($column);
    }

    public function testColumnDDL()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $column = new class extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver): string
            {
                return 'CUSTOM_COLUMN';
            }
        };
        $this->assertSame('CUSTOM_COLUMN', $builder->getColumnDDL($column));

        $column = new class (['default' => null]) extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver): string
            {
                return 'CUSTOM_COLUMN' . ( isset($this->default) ? ' DEFAULT ' . (null === $this->default ? 'NULL' : "'" . $this->default . "'") : null );
            }
        };
        $this->assertSame('CUSTOM_COLUMN DEFAULT NULL', $builder->getColumnDDL($column));

        $column = new class (['default' => 'foo']) extends Column {
            public function getColumnDdlByDriver(DriverInterface $driver): string
            {
                return 'CUSTOM_COLUMN' . ( isset($this->default) ? ' DEFAULT ' . (null === $this->default ? 'NULL' : "'" . $this->default . "'") : null );
            }
        };
        $this->assertSame('CUSTOM_COLUMN DEFAULT \'foo\'', $builder->getColumnDDL($column));
    }

}

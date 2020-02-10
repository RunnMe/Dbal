<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Indexes;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\DriverInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Index;

class CustomIndexTest extends TestCase
{

    public function testNullIndexDDL()
    {
        $driver = new Driver();
        $index = new class(['table' => 't1', 'columns' => ['foo']]) extends Index {};

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Return value of Runn\Dbal\Drivers\Sqlite\QueryBuilder::getIndexDDL() must be of the type string, null returned');
        $driver->getQueryBuilder()->getIndexDDL($index);
    }

    public function testIndexDDL()
    {
        $driver = new Driver();

        $index = new class(['table' => 't1', 'columns' => ['foo']]) extends Index
        {
            public function getIndexDdlByDriver(DriverInterface $driver)
            {
                return 'CUSTOM_INDEX';
            }
        };
        $this->assertSame('CUSTOM_INDEX', $driver->getQueryBuilder()->getIndexDDL($index));
    }

}

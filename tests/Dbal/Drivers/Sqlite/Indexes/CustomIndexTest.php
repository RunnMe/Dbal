<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Indexes;

use Runn\Dbal\DriverInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Index;

class CustomIndexTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Error
     * Return value of Runn\Dbal\Drivers\Sqlite\Driver::getIndexDDL() must be of the type string, null returned
     */
    public function testNullIndexDDL()
    {
        $driver = new Driver();

        $index = new class(['table' => 't1', 'columns' => ['foo']]) extends Index {};
        $this->assertNull($driver->getQueryBuilder()->getIndexDDL($index));
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
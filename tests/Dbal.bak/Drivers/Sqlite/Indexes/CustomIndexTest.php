<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Indexes;

use Running\Dbal\DriverInterface;
use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Index;

class CustomIndexTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Error
     * Return value of Running\Dbal\Drivers\Sqlite\Driver::getIndexDDL() must be of the type string, null returned
     */
    public function testNullIndexDDL()
    {
        $driver = new Driver();

        $index = new class(['columns' => ['foo']]) extends Index {};
        $this->assertNull($driver->getIndexDDL('t1', $index));
    }

    public function testIndexDDL()
    {
        $driver = new Driver();

        $index = new class(['columns' => ['foo']]) extends Index
        {
            public function getIndexDdlByDriver(DriverInterface $driver)
            {
                return 'CUSTOM_INDEX';
            }
        };
        $this->assertSame('CUSTOM_INDEX', $driver->getIndexDDL('t1', $index));
    }

}
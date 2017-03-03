<?php

namespace Running\tests\Dbal\Drivers;

use Running\Dbal\DriverInterface;
use Running\Dbal\Drivers;
use Running\Dbal\DriverBuilderInterface;

class DriversTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Driver class "invalid" does not exist
     */
    public function testInvalidDriver()
    {
        $driver = Drivers::instance('invalid');
        $this->fail();
    }

    public function testVaildDriver()
    {
        $driver = Drivers::instance(\Running\Dbal\Drivers\Sqlite\Driver::class);

        $this->assertInstanceOf(DriverInterface::class, $driver);
        $this->assertInstanceOf(Drivers\Sqlite\Driver::class, $driver);
    }

}
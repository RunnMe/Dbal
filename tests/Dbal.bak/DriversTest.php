<?php

namespace Running\tests\Dbal\Drivers;

use Running\Dbal\DriverInterface;
use Running\Dbal\Drivers;
use Running\Dbal\DriverBuilderInterface;

class DriversTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Driver class "notexists" does not exist
     */
    public function testDriverNotExists()
    {
        $driver = Drivers::instance('notexists');
        $this->fail();
    }

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Class "Running\tests\Dbal\Drivers\DriversTest" is not a DBAL driver
     */
    public function testDriverInvalid()
    {
        $driver = Drivers::instance(self::class);
        $this->fail();
    }

    public function testVaildDriver()
    {
        $driver = Drivers::instance(\Running\Dbal\Drivers\Sqlite\Driver::class);

        $this->assertInstanceOf(DriverInterface::class, $driver);
        $this->assertInstanceOf(Drivers\Sqlite\Driver::class, $driver);
    }

}
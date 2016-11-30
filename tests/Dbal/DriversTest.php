<?php

namespace Running\tests\Dbal\Drivers;

use Running\Dbal\Drivers;
use Running\Dbal\IDriver;

class DriversTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Class Running\Dbal\Drivers\Invalid does not exist
     */
    public function testInvalidDriver()
    {
        $driver = Drivers::instance('invalid');
        $this->fail();
    }

    public function testVaildDriver()
    {
        $driver = Drivers::instance('sqlite');

        $this->assertInstanceOf(IDriver::class, $driver);
        $this->assertInstanceOf(Drivers\Sqlite::class, $driver);
    }

}
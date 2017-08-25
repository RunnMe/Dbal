<?php

namespace Runn\tests\Dbal\Driver;

use Runn\Dbal\Columns\IntColumn;

require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';
class testDriver extends \Runn\tests\Dbal\Drivers\WithoutDsn\Driver {
    public function __construct($val = null) {
        echo $val;
    }
}

class DriverTest extends \PHPUnit_Framework_TestCase
{

    public function testInstanceNull()
    {
        $driver = testDriver::instance();
        $this->assertInstanceOf(testDriver::class, $driver);

        $this->expectOutputString('Foo');
        $driver = testDriver::instance(null, 'Foo');
        $this->assertInstanceOf(testDriver::class, $driver);
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Driver class "Invalid\Class" does not exists
     */
    public function testInstanceClassNotExists()
    {
        $driver = testDriver::instance('Invalid\Class');
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Class "stdClass" is not a DBAL driver
     */
    public function testInstanceInvalidDriverClass()
    {
        $driver = testDriver::instance(\stdClass::class);
    }

    public function testProcessValueAfterLoad()
    {
        $driver = testDriver::instance();
        $value = 42;
        $this->assertSame($value, $driver->processValueAfterLoad(new IntColumn(), $value));
    }

    public function testProcessValueBeforeSave()
    {
        $driver = testDriver::instance();
        $value = 42;
        $this->assertSame($value, $driver->processValueBeforeSave(new IntColumn(), $value));
    }

}
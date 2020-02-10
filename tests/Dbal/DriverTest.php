<?php

namespace Runn\tests\Dbal\Driver;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns\IntColumn;
use Runn\Dbal\Exception;

require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';
class testDriver extends \Runn\tests\Dbal\Drivers\WithoutDsn\Driver {
    public function __construct($val = null) {
        echo $val;
    }
}

class DriverTest extends TestCase
{

    public function testInstanceNull()
    {
        $driver = testDriver::instance();
        $this->assertInstanceOf(testDriver::class, $driver);

        $this->expectOutputString('Foo');
        $driver = testDriver::instance(null, 'Foo');
        $this->assertInstanceOf(testDriver::class, $driver);
    }

    public function testInstanceClassNotExists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Driver class "Invalid\Class" does not exists');
        $driver = testDriver::instance('Invalid\Class');
    }

    public function testInstanceInvalidDriverClass()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class "stdClass" is not a DBAL driver');
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

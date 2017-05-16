<?php

namespace Runn\tests\Dbal\Column;

use Runn\Dbal\Column;
use Runn\Dbal\DriverInterface;

class testColumn1 extends Column {}

class testColumn2 extends Column {
    public function processValueAfterLoad(DriverInterface $driver, $value)
    {
        return (int)$value;
    }
    public function processValueBeforeSave(DriverInterface $driver, $value)
    {
        return (string)$value;
    }
}

require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';
class testDriver extends \Runn\tests\Dbal\Drivers\WithoutDsn\Driver {}

class ColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $column = new testColumn2();
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testProcessValueAfterLoad()
    {
        $column = new testColumn1();
        $this->assertSame('42', $column->processValueAfterLoad(new testDriver(), '42'));
        $column = new testColumn2();
        $this->assertSame(42, $column->processValueAfterLoad(new testDriver(), '42'));
    }

    public function testProcessValueBeforeSave()
    {
        $column = new testColumn1();
        $this->assertSame(42, $column->processValueBeforeSave(new testDriver(), 42));
        $column = new testColumn2();
        $this->assertSame('42', $column->processValueBeforeSave(new testDriver(), 42));
    }

}
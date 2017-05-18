<?php

namespace Runn\tests\Dbal\Index;

use Runn\Core\Exceptions;
use Runn\Core\Std;
use Runn\Dbal\Index;
use Runn\Validation\Exceptions\EmptyValue;
use Runn\Validation\Exceptions\InvalidArray;

class IndexTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $index = new class(['columns' => ['foo']]) extends Index {};

        $this->assertInstanceOf(Index::class, $index);
        $this->assertInstanceOf(Std::class, $index);

        $this->assertSame(['foo'], $index->columns);
    }

    public function testNeedCasting()
    {
        $index = new class(['columns' => ['foo'], 'option' => 'sample', 'options' => ['test', 'example']]) extends Index {};

        $this->assertSame(['foo'], $index->columns);

        $this->assertSame('sample', $index->option);
        $this->assertSame(['test', 'example'], $index->options);
    }

    public function testEmptyColumns()
    {
        try {
            $index = new class(['columns' => []]) extends Index {};
            $this->fail();
        } catch (Exceptions $errors) {
            $this->assertInstanceOf(EmptyValue::class, $errors[0]);
            return;
        }
        $this->fail();
    }

    public function testInvalidColumns()
    {
        try {
            $index = new class(['columns' => 'foo']) extends Index {};
            $this->fail();
        } catch (Exceptions $errors) {
            $this->assertInstanceOf(InvalidArray::class, $errors[0]);
            return;
        }
        $this->fail();
    }

}
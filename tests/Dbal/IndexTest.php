<?php

namespace Running\tests\Dbal\Index;

use Running\Core\MultiException;
use Running\Core\Std;
use Running\Dbal\Index;
use Running\Validation\Exceptions\EmptyValue;
use Running\Validation\Exceptions\InvalidArray;

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

    public function testRequiredColumns()
    {
        try {
            $index = new class() extends Index {};
            $this->fail();
        } catch (MultiException $errors) {
            $this->assertContains('You need at least one of', $errors[0]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testEmptyColumns()
    {
        try {
            $index = new class(['columns' => []]) extends Index {};
            $this->fail();
        } catch (MultiException $errors) {
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
        } catch (MultiException $errors) {
            $this->assertInstanceOf(InvalidArray::class, $errors[0]);
            return;
        }
        $this->fail();
    }

}
<?php

namespace Running\tests\Dbal\Columns;

use Running\Core\CollectionInterface;
use Running\Core\TypedCollection;
use Running\Core\TypedCollectionInterface;
use Running\Dbal\Columns;

class ColumnsTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $columns = new Columns();
        $this->assertInstanceOf(Columns::class, $columns);
        $this->assertInstanceOf(TypedCollection::class, $columns);
        $this->assertInstanceOf(TypedCollectionInterface::class, $columns);
        $this->assertInstanceOf(CollectionInterface::class, $columns);
    }

    public function testAddValid()
    {
        $columns = new Columns();
        $columns->add(new Columns\BooleanColumn());
        $columns[] = new Columns\IntColumn();

        $this->assertCount(2, $columns);
    }

    /**
     * @expectedException \Running\Core\Exception
     * @expectedExceptionMessage Typed collection class mismatch
     */
    public function testAddInValid()
    {
        $columns = new Columns();
        $columns->add(new \stdClass());
        $this->fail();
    }

    public function testFromArray()
    {
        $columns = new Columns([
            'foo' => ['class' => Columns\BooleanColumn::class, 'default' => 1],
            'bar' => ['class' => Columns\StringColumn::class, 'default' => null],
            'baz' => ['class' => Columns\IntColumn::class, 'bytes' => 4]
        ]);

        $this->assertEquals(3, count($columns));

        $this->assertInstanceOf(Columns\BooleanColumn::class, $columns['foo']);
        $this->assertSame(1, $columns['foo']->default);
        $this->assertInstanceOf(Columns\StringColumn::class, $columns['bar']);
        $this->assertSame(null, $columns['bar']->default);
        $this->assertInstanceOf(Columns\IntColumn::class, $columns['baz']);
        $this->assertSame(4, $columns['baz']->bytes);
    }

}
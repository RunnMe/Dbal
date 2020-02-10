<?php

namespace Runn\tests\Dbal\Columns;

use PHPUnit\Framework\TestCase;
use Runn\Core\CollectionInterface;
use Runn\Core\Std;
use Runn\Core\TypedCollection;
use Runn\Core\TypedCollectionInterface;
use Runn\Dbal\Columns;

class ColumnsTest extends TestCase
{

    public function testInstance()
    {
        $columns = new Columns();
        $this->assertInstanceOf(Columns::class, $columns);
        $this->assertInstanceOf(TypedCollection::class, $columns);
        $this->assertInstanceOf(TypedCollectionInterface::class, $columns);
        $this->assertInstanceOf(CollectionInterface::class, $columns);
    }

    public function testConstructCast()
    {
        $columns = new Columns([
            'foo' => ['class' => Columns\BooleanColumn::class, 'default' => 1],
            'bar' => new Std(['class' => Columns\StringColumn::class, 'default' => null]),
            'baz' => new Columns\IntColumn(['bytes' => 4]),
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

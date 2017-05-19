<?php

namespace Runn\tests\Dbal\Indexes;

use Runn\Core\CollectionInterface;
use Runn\Core\Std;
use Runn\Core\TypedCollection;
use Runn\Core\TypedCollectionInterface;
use Runn\Dbal\Indexes;

class IndexesTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $indexes = new Indexes();
        $this->assertInstanceOf(Indexes::class, $indexes);
        $this->assertInstanceOf(TypedCollection::class, $indexes);
        $this->assertInstanceOf(TypedCollectionInterface::class, $indexes);
        $this->assertInstanceOf(CollectionInterface::class, $indexes);
    }

    public function testConstructCast()
    {
        $indexes = new Indexes([
            'foo' => ['class' => Indexes\SimpleIndex::class, 'table' => 'table1'],
            'bar' => new Std(['class' => Indexes\UniqueIndex::class, 'columns' => ['col1', 'col2']]),
            'baz' => new Indexes\SimpleIndex(['name' => 'test']),
        ]);

        $this->assertEquals(3, count($indexes));

        $this->assertInstanceOf(Indexes\SimpleIndex::class, $indexes['foo']);
        $this->assertSame('table1', $indexes['foo']->table);
        $this->assertInstanceOf(Indexes\UniqueIndex::class, $indexes['bar']);
        $this->assertSame(['col1', 'col2'], $indexes['bar']->columns);
        $this->assertInstanceOf(Indexes\SimpleIndex::class, $indexes['baz']);
        $this->assertSame('test', $indexes['baz']->name);
    }

}
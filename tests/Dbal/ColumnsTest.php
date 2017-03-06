<?php

namespace Running\tests\Dbal\Drivers\Sqlite\Columns;

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

}
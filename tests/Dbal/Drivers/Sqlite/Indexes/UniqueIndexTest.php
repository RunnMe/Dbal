<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Indexes\UniqueIndex;

class UniqueIndexTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexDLL()
    {
        $driver = new Driver();
        $index = new UniqueIndex(['columns' => ['foo'], 'bar' => 'baz']);
        $this->assertSame('UNIQUE INDEX foo_idx ON t1 (foo)', $driver->getIndexDDL('t1', $index));
        $this->assertEquals('foo_idx', $index->name);
        $index = new UniqueIndex(['columns' => ['foo', 'baz'], 'bar' => 'baz']);
        $this->assertSame('UNIQUE INDEX foo_baz_idx ON t1 (foo, baz)', $driver->getIndexDDL('t1', $index));
        $index = new UniqueIndex(['columns' => ['foo', 'baz'], 'order' => 'desc', 'bar' => 'baz']);
        $this->assertSame('UNIQUE INDEX DESC.foo_baz_idx ON t1 (foo, baz)', $driver->getIndexDDL('t1', $index));
        $index = new UniqueIndex(['columns' => ['foo', 'baz'], 'order' => '42', 'bar' => 'baz']);
        $this->assertSame('UNIQUE INDEX foo_baz_idx ON t1 (foo, baz)', $driver->getIndexDDL('t1', $index));
    }
}

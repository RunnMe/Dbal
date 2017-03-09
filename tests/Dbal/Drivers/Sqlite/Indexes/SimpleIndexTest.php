<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Indexes\SimpleIndex;

class SimpleIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();
        $index = new SimpleIndex(['columns' => ['foo'], 'table' => 't1', 'bar' => 'baz']);
        $this->assertSame('INDEX foo_idx ON t1 (foo)', $driver->getIndexDDL($index));
        $index = new SimpleIndex(['columns' => ['foo', 'baz'], 'table' => 't1', 'bar' => 'baz']);
        $this->assertSame('INDEX foo_baz_idx ON t1 (foo, baz)', $driver->getIndexDDL($index));
        $index = new SimpleIndex(['columns' => ['foo', 'baz'], 'schema' => 's1', 'table' => 't1', 'bar' => 'baz']);
        $this->assertSame('INDEX s1.foo_baz_idx ON t1 (foo, baz)', $driver->getIndexDDL($index));
    }
}

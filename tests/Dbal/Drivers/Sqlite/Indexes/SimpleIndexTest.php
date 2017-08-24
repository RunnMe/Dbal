<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Indexes\SimpleIndex;

class SimpleIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();

        $index = new SimpleIndex(['table' => 't1', 'columns' => ['foo']]);
        $this->assertSame('INDEX `foo_idx` ON `t1` (`foo`)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new SimpleIndex(['table' => 't1', 'columns' => ['foo', 'bar']]);
        $this->assertSame('INDEX `foo_bar_idx` ON `t1` (`foo`, `bar`)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_bar_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new SimpleIndex(['table' => 't1', 'columns' => ['foo', '`bar` ASC', '"baz" desc']]);
        $this->assertSame('INDEX `foo_bar_baz_idx` ON `t1` (`foo`, `bar` ASC, `baz` DESC)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_bar_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);
    }

}

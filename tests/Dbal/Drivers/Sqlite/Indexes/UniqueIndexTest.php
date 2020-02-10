<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Indexes\UniqueIndex;

class UniqueIndexTest extends TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();

        $index = new UniqueIndex(['table' => 't1', 'columns' => ['foo']]);
        $this->assertSame('UNIQUE INDEX `foo_idx` ON `t1` (`foo`)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['table' => 't1', 'columns' => ['foo', 'bar']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_idx` ON `t1` (`foo`, `bar`)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_bar_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['table' => 't1', 'columns' => ['foo', '`bar` ASC', '"baz" desc']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_baz_idx` ON `t1` (`foo`, `bar` ASC, `baz` DESC)', $driver->getQueryBuilder()->getIndexDDL($index));
        $this->assertSame('foo_bar_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);

    }

}

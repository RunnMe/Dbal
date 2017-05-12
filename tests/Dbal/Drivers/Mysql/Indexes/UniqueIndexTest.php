<?php

namespace Dbal\Drivers\Mysql\Indexes;

use Running\Dbal\Drivers\Mysql\Driver;
use Running\Dbal\Indexes\UniqueIndex;

class UniqueIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();

        $index = new UniqueIndex(['columns' => ['foo']]);
        $this->assertSame('UNIQUE INDEX `foo_idx` (`foo`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['columns' => ['foo', 'bar']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_idx` (`foo`, `bar`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['columns' => ['foo', '`bar` ASC', '`baz` desc']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_baz_idx` (`foo`, `bar` ASC, `baz` DESC)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['columns' => ['foo', '"baz(10)" desc']]);
        $this->assertSame('UNIQUE INDEX `foo_baz_idx` (`foo`, baz(10) DESC)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);
    }
}

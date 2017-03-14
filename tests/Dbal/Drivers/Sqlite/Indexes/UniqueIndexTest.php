<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Indexes\UniqueIndex;

class UniqueIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();

        $index = new UniqueIndex(['columns' => ['foo']]);
        $this->assertSame('UNIQUE INDEX `foo_idx` ON `t1` (`foo`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['columns' => ['foo', 'bar']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_idx` ON `t1` (`foo`, `bar`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new UniqueIndex(['columns' => ['foo', '`bar` ASC', '"baz" desc']]);
        $this->assertSame('UNIQUE INDEX `foo_bar_baz_idx` ON `t1` (`foo`, `bar` ASC, `baz` DESC)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);

    }

}

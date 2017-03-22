<?php

namespace Dbal\Drivers\Mysql\Indexes;

use Running\Dbal\Drivers\Mysql\Driver;
use Running\Dbal\Indexes\FulltextIndex;

class FulltextIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();

        $index = new FulltextIndex(['columns' => ['foo']]);
        $this->assertSame('FULLTEXT INDEX `foo_idx` ON `t1` (`foo`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new FulltextIndex(['columns' => ['foo', 'bar']]);
        $this->assertSame('FULLTEXT INDEX `foo_bar_idx` ON `t1` (`foo`, `bar`)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new FulltextIndex(['columns' => ['foo', '`bar` ASC', '"baz" desc']]);
        $this->assertSame('FULLTEXT INDEX `foo_bar_baz_idx` ON `t1` (`foo`, `bar` ASC, `baz` DESC)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_bar_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);

        $index = new FulltextIndex(['columns' => ['foo', '"baz(10)" desc']]);
        $this->assertSame('FULLTEXT INDEX `foo_baz_idx` ON `t1` (`foo`, baz(10) DESC)', $driver->getIndexDDL('t1', $index));
        $this->assertSame('foo_baz_idx', $index->name);
        $this->assertSame('t1', $index->table);
    }
}

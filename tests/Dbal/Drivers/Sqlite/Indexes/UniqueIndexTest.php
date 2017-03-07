<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Running\Core\Std;
use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Indexes\UniqueIndex;

class UniqueIndexTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexDLL()
    {
        $driver = new Driver();
        $index = new UniqueIndex(['columns' => ['foo', 'baz']]);
        $this->assertSame('UNIQUE INDEX', $driver->getIndexDDL($index));
    }
}

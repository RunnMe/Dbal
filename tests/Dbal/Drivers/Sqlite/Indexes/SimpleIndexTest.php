<?php

namespace Dbal\Drivers\Sqlite\Indexes;

use Running\Core\Std;
use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Indexes\SimpleIndex;

class SimpleIndexTest extends \PHPUnit_Framework_TestCase
{

    public function testIndexDLL()
    {
        $driver = new Driver();
        $index = new SimpleIndex(['columns' => ['foo', 'baz']]);
        $this->assertSame('INDEX', $driver->getIndexDDL($index));
    }
}

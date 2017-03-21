<?php

namespace Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\Query;

class DriverInsertTest extends \PHPUnit_Framework_TestCase
{

    public function testInsert()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $driver->createTable(
            $connection,
            'foo',
            new Columns([
                'id' => ['class' => Columns\SerialColumn::class],
                'bar' => ['class' => Columns\StringColumn::class]])
        );
        $result = $connection->query(new Query('SELECT COUNT(*) FROM foo'))->fetchAll()[0];
        $this->assertEquals(0, $result['COUNT(*)']);
        $id = $driver->insert($connection, 'foo', ['bar' => '42']);
        $result = $connection->query(new Query('SELECT * FROM foo'))->fetchAll()[0];
        $this->assertEquals($id, $result['id']);
        $this->assertEquals('42', $result['bar']);
    }
}

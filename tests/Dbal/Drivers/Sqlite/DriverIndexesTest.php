<?php

namespace Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Dbal\Connection;
use Running\Dbal\Indexes\SimpleIndex;
use Running\Dbal\Indexes\UniqueIndex;
use Running\Dbal\Query;

class DriverIndexesTest extends \PHPUnit_Framework_TestCase
{

    public function testAddIndex()
    {
        /*
        $connection = new Connection(
            new Config(
                ['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']
            )
        );
        $connection->execute(new Query('CREATE TABLE testtable1 (foo INT, bar TEXT)'));
        $fooIndex = new SimpleIndex(['columns' => ['foo'], 'name' => 'fooindex']);
        $barIndex = new UniqueIndex(['columns' => ['bar'], 'name' => 'barindex']);
        $driver = $connection->getDriver();
        $driver->addIndex($connection, 'testtable1', [$fooIndex, $barIndex]);
        $indexes = $connection->query(new Query('PRAGMA index_list(testtable1)'))->fetchAll();
        $this->assertEquals(2, count($indexes));
        $this->assertEquals('barindex', $indexes[0]['name']);
        $this->assertEquals('1', $indexes[0]['unique']);
        $this->assertEquals('fooindex', $indexes[1]['name']);
        $this->assertEquals('0', $indexes[1]['unique']);
        $index = $connection->query(new Query('PRAGMA index_info(fooindex)'))->fetchAll()[0];
        $this->assertEquals('foo', $index['name']);
        $index = $connection->query(new Query('PRAGMA index_info(barindex)'))->fetchAll()[0];
        $this->assertEquals('bar', $index['name']);
        */
    }

    public function testDropIndex()
    {
        /*
        $connection = new Connection(
            new Config(
                ['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']
            )
        );
        $connection->execute(new Query('CREATE TABLE testtable1 (foo INT, bar TEXT)'));
        $fooIndex = new SimpleIndex(['columns' => ['foo'], 'name' => 'fooindex']);
        $barIndex = new UniqueIndex(['columns' => ['bar'], 'name' => 'barindex']);
        $driver = $connection->getDriver();
        $driver->addIndex($connection, 'testtable1', [$fooIndex, $barIndex]);
        $indexes = $connection->query(new Query('PRAGMA index_list(testtable1)'))->fetchAll();
        $this->assertEquals(2, count($indexes));
        $driver->dropIndex($connection, 'testtable1', [$fooIndex, $barIndex]);
        $indexes = $connection->query(new Query('PRAGMA index_list(testtable1)'))->fetchAll();
        $this->assertEquals(0, count($indexes));
        */
    }


}

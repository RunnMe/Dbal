<?php

namespace Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Dbal\Columns;
use Running\Dbal\Columns\IntColumn;
use Running\Dbal\Connection;
use Running\Dbal\Query;

class DriverColumnsTest extends \PHPUnit_Framework_TestCase
{

    public function testAddColumn()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
        $column = new IntColumn(['bytes' => 4]);
        $driver->addColumn($connection, 'foo', 'bar', $column);
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(2, count($columnsInfo));
        $this->assertEquals('bar', $columnsInfo[1]['name']);
        $this->assertEquals('INTEGER', $columnsInfo[1]['type']);
    }

    public function testAddColumns()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
        $columns = new Columns([
            'foo' => ['class' => Columns\BooleanColumn::class, 'default' => 1],
            'bar' => ['class' => Columns\StringColumn::class, 'default' => null],
            'baz' => ['class' => Columns\IntColumn::class, 'bytes' => 4]
        ]);
        $driver->addColumns($connection, 'foo', $columns);
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(4, count($columnsInfo));
        $this->assertEquals('foo', $columnsInfo[1]['name']);
        $this->assertEquals('bar', $columnsInfo[2]['name']);
        $this->assertEquals('baz', $columnsInfo[3]['name']);
    }

    public function testDropColumn()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
        $driver->addColumn($connection, 'foo', 'bar', new IntColumn(['default' => 1]));
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(2, count($columnsInfo));
        $driver->dropColumn($connection, 'foo', 'bar');
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
    }

    public function testDropColumns()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
        $columns = new Columns([
            'bar' => ['class' => Columns\StringColumn::class, 'default' => null],
            'baz' => ['class' => Columns\IntColumn::class, 'bytes' => 4]
        ]);
        $driver->addColumns($connection, 'foo', $columns);
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(3, count($columnsInfo));
        $driver->dropColumns($connection, 'foo', ['bar', 'baz']);
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(1, count($columnsInfo));
    }

    public function testRenameColumns()
    {
        $connection = new Connection(
            new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:'])
        );
        $driver = $connection->getDriver();
        $connection->execute(new Query('CREATE TABLE foo (id SERIAL, baz TEXT)'));
        $driver->renameColumn($connection, 'foo', 'id', 'newid');
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $this->assertEquals(2, count($columnsInfo));
        $this->assertEquals('newid', $columnsInfo[0]['name']);
    }
}

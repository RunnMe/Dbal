<?php

namespace Running\tests\Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Dbal\Columns;
use Running\Dbal\Columns\StringColumn;
use Running\Dbal\Connection;
use Running\Dbal\Drivers\Sqlite\Driver;
use Running\Dbal\Query;

class DriverTablesTest extends \PHPUnit_Framework_TestCase
{

    public function testExistsTable()
    {
        $connection = new Connection(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']));
        $driver = $connection->getDriver();

        $this->assertFalse($driver->existsTable($connection, 'foo'));

        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));

        $this->assertTrue($driver->existsTable($connection, 'foo'));
    }

    public function testCreateTableDDL()
    {
        $driver = new Driver();
        $method = new \ReflectionMethod(Driver::class, 'createTableDdl');
        $method->setAccessible(true);

        $columns = new Columns([
            'foo' => ['class' => StringColumn::class]
        ]);

        $this->assertSame("CREATE TABLE `test`\n(\n`foo` TEXT\n)", $method->invoke($driver, 'test', $columns));
    }

    public function testCreateTable()
    {
        $connection = new Connection(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']));
        $driver = $connection->getDriver();
        $this->assertFalse($driver->existsTable($connection, 'foo'));

        $driver->createTable($connection, 'foo', new Columns(['num' => ['class' => Columns\IntColumn::class], 'name' => ['class' => Columns\StringColumn::class]]));
        $this->assertTrue($driver->existsTable($connection, 'foo'));

        $info = $connection->query(new Query("PRAGMA table_info('foo') "))->fetchAll();
        $this->assertCount(2, $info);
        $this->assertEquals('num', $info[0]['name']);
        $this->assertEquals('INTEGER', $info[0]['type']);
        $this->assertEquals('name', $info[1]['name']);
        $this->assertEquals('TEXT', $info[1]['type']);
    }

    public function testDropTable()
    {
        $connection = new Connection(new Config(['driver' => \Running\Dbal\Drivers\Sqlite\Driver::class, 'file' => ':memory:']));
        $driver = $connection->getDriver();
        $this->assertFalse($driver->existsTable($connection, 'foo'));

        $connection->execute(new Query('CREATE TABLE foo (id SERIAL)'));
        $this->assertTrue($driver->existsTable($connection, 'foo'));

        $driver->dropTable($connection, 'foo');
        $this->assertFalse($driver->existsTable($connection, 'foo'));
    }


}

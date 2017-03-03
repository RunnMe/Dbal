<?php

namespace Running\tests\Dbal\Drivers\Sqlite;

use Running\Core\Config;
use Running\Dbal\Connection;
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

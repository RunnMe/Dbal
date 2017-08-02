<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Driver;

use Runn\Dbal\Columns;
use Runn\Dbal\Dbh;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Drivers\Sqlite\QueryBuilder;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Query;

class DriverTest extends \PHPUnit_Framework_TestCase
{

    public function testGetQueryBuilder()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $this->assertInstanceOf(QueryBuilder::class, $builder);
        $this->assertInstanceOf(DriverQueryBuilderInterface::class, $builder);
    }

    public function testGetExistsTableQuery()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $query = $driver->getExistsTableQuery('foo');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertSame(
            "SELECT count(*)>0\nFROM `sqlite_master` AS t1\nWHERE type=:type AND name=:name",
            $builder->makeQueryString($query)
        );

        $this->assertSame(
            [
                ['name' => ':type', 'value' => 'table', 'type' => Dbh::DEFAULT_PARAM_TYPE],
                ['name' => ':name', 'value' => 'foo',   'type' => Dbh::DEFAULT_PARAM_TYPE],
            ],
            $query->getParams()
        );
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty table name
     */
    public function testGetCreateTableQueryEmptyName()
    {
        $driver = new Driver();
        $driver->getCreateTableQuery('');
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty columns list
     */
    public function testGetCreateTableQueryNullColumns()
    {
        $driver = new Driver();
        $driver->getCreateTableQuery('foo');
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty columns list
     */
    public function testGetCreateTableQueryEmptyColumns()
    {
        $driver = new Driver();
        $driver->getCreateTableQuery('foo', new Columns());
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty column name
     */
    public function testGetCreateTableQueryEmptyColumnName()
    {
        $driver = new Driver();
        $driver->getCreateTableQuery('foo', new Columns([
            new Columns\IntColumn,
        ]));
    }

    public function testGetCreateTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getCreateTableQuery('test', new Columns([
            'foo' => new Columns\SerialColumn(),
            'bar' => new Columns\StringColumn(['default' => 'oops']),
            new Columns\IntColumn(['name' => 'baz', 'default' => 0]),
        ]));

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "CREATE TABLE `test`\n(\n`foo` INTEGER AUTOINCREMENT,\n`bar` TEXT DEFAULT 'oops',\n`baz` INTEGER DEFAULT 0\n)",
            $query->string
        );
    }

}

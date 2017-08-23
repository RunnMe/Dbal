<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Driver;

use Runn\Dbal\Columns;
use Runn\Dbal\Dbh;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Drivers\Sqlite\QueryBuilder;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Queries;
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
            'foo' => new Columns\PkColumn(),
            'bar' => new Columns\StringColumn(['default' => 'oops']),
            new Columns\IntColumn(['name' => 'baz', 'default' => 0]),
        ]));

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "CREATE TABLE `test`\n(\n`foo` INTEGER PRIMARY KEY AUTOINCREMENT,\n`bar` TEXT DEFAULT 'oops',\n`baz` INTEGER DEFAULT 0\n)",
            $query->string
        );
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty old table name
     */
    public function testGetRenameTableQueryEmptyOldName()
    {
        $driver = new Driver();
        $driver->getRenameTableQuery('', 'foo');
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty new table name
     */
    public function testGetRenameTableQueryEmptyNewName()
    {
        $driver = new Driver();
        $driver->getRenameTableQuery('foo', '');
    }

    public function testGetRenameTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getRenameTableQuery('foo', 'bar');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "ALTER TABLE `foo` RENAME TO `bar`",
            $query->string
        );
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty table name
     */
    public function testGetTruncateTableQueryEmptyName()
    {
        $driver = new Driver();
        $driver->getTruncateTableQuery('');
    }

    public function testGetTruncateTableQuery()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $query = $driver->getTruncateTableQuery('foo');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Queries::class, $query);

        $this->assertFalse($query[0]->isString());
        $this->assertSame(
            "DELETE FROM `foo`",
            $builder->makeQueryString($query[0])
        );

        $this->assertFalse($query[1]->isString());
        $this->assertSame(
            "UPDATE `SQLITE_SEQUENCE`\nSET `seq`=0\nWHERE name=:name",
            $builder->makeQueryString($query[1])
        );
        $this->assertSame(
            [['name' => ':name', 'value' => 'foo', 'type' => Dbh::PARAM_STR]],
            $query[1]->getParams()
        );
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty table name
     */
    public function testGetDropTableQueryEmptyName()
    {
        $driver = new Driver();
        $driver->getDropTableQuery('');
    }

    public function testGetDropTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getDropTableQuery('foo');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "DROP TABLE `foo`",
            $query->string
        );
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty table name
     */
    public function testGetAddColumnQueryEmptyTableName()
    {
        $driver = new Driver();
        $driver->getAddColumnQuery('', new Columns\IntColumn());
    }

    /**
     * @expectedException \Runn\Dbal\Drivers\Exception
     * @expectedExceptionMessage Empty column name
     */
    public function testGetAddColumnQueryEmptyColumnName()
    {
        $driver = new Driver();
        $driver->getAddColumnQuery('table', new Columns\IntColumn());
    }

    public function testGetAddColumnQuery()
    {
        $driver = new Driver();
        $query = $driver->getAddColumnQuery('foo', new Columns\IntColumn(['name' => 'bar']));

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "ALTER TABLE `foo` ADD COLUMN `bar` INTEGER",
            $query->string
        );
    }

}

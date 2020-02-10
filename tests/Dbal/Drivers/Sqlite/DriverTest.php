<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Driver;

use PHPUnit\Framework\TestCase;
use Runn\Dbal\Columns;
use Runn\Dbal\Dbh;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Drivers\Exception;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Drivers\Sqlite\QueryBuilder;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Queries;
use Runn\Dbal\Query;

class DriverTest extends TestCase
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

        $query = $builder->getExistsTableQuery('foo');

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

    public function testGetCreateTableQueryEmptyName()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty table name');
        $builder->getCreateTableQuery('');
    }

    public function testGetCreateTableQueryNullColumns()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty columns list');
        $driver->getQueryBuilder()->getCreateTableQuery('foo');
    }

    public function testGetCreateTableQueryEmptyColumns()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty columns list');
        $driver->getQueryBuilder()->getCreateTableQuery('foo', new Columns());
    }

    public function testGetCreateTableQueryEmptyColumnName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty column name');
        $driver->getQueryBuilder()->getCreateTableQuery('foo', new Columns([
            new Columns\IntColumn,
        ]));
    }

    public function testGetCreateTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getQueryBuilder()->getCreateTableQuery('test', new Columns([
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

    public function testGetRenameTableQueryEmptyOldName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty old table name');
        $driver->getQueryBuilder()->getRenameTableQuery('', 'foo');
    }

    public function testGetRenameTableQueryEmptyNewName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty new table name');
        $driver->getQueryBuilder()->getRenameTableQuery('foo', '');
    }

    public function testGetRenameTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getQueryBuilder()->getRenameTableQuery('foo', 'bar');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "ALTER TABLE `foo` RENAME TO `bar`",
            $query->string
        );
    }

    public function testGetTruncateTableQueryEmptyName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty table name');
        $driver->getQueryBuilder()->getTruncateTableQuery('');
    }

    public function testGetTruncateTableQuery()
    {
        $driver = new Driver();
        $builder = $driver->getQueryBuilder();

        $queries = $driver->getQueryBuilder()->getTruncateTableQuery('foo');

        $this->assertInstanceOf(ExecutableInterface::class, $queries);
        $this->assertInstanceOf(Queries::class, $queries);

        $this->assertCount(2, $queries);

        $this->assertFalse($queries[0]->isString());
        $this->assertSame(
            "DELETE FROM `foo`",
            $builder->makeQueryString($queries[0])
        );

        $this->assertFalse($queries[1]->isString());
        $this->assertSame(
            "UPDATE `SQLITE_SEQUENCE`\nSET `seq`=0\nWHERE name=:name",
            $builder->makeQueryString($queries[1])
        );
        $this->assertSame(
            [['name' => ':name', 'value' => 'foo', 'type' => Dbh::PARAM_STR]],
            $queries[1]->getParams()
        );
    }

    public function testGetDropTableQueryEmptyName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty table name');
        $driver->getQueryBuilder()->getDropTableQuery('');
    }

    public function testGetDropTableQuery()
    {
        $driver = new Driver();
        $query = $driver->getQueryBuilder()->getDropTableQuery('foo');

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "DROP TABLE `foo`",
            $query->string
        );
    }

    public function testGetAddColumnQueryEmptyTableName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty table name');
        $driver->getQueryBuilder()->getAddColumnQuery('', new Columns\IntColumn());
    }

    public function testGetAddColumnQueryEmptyColumnName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty column name');
        $driver->getQueryBuilder()->getAddColumnQuery('table', new Columns\IntColumn());
    }

    public function testGetAddColumnQuery()
    {
        $driver = new Driver();
        $query = $driver->getQueryBuilder()->getAddColumnQuery('foo', new Columns\IntColumn(['name' => 'bar']));

        $this->assertInstanceOf(ExecutableInterface::class, $query);
        $this->assertInstanceOf(Query::class, $query);

        $this->assertTrue($query->isString());
        $this->assertSame(
            "ALTER TABLE `foo` ADD COLUMN `bar` INTEGER",
            $query->string
        );
    }

    public function testGetAddColumnsQueryEmptyTableName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty table name');
        $driver->getQueryBuilder()->getAddColumnsQuery('', new Columns([new Columns\IntColumn()]));
    }

    public function testGetAddColumnsQueryEmptyColumnName()
    {
        $driver = new Driver();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty column name');
        $driver->getQueryBuilder()->getAddColumnsQuery('foo', new Columns([new Columns\IntColumn()]));
    }

    public function  testGetAddColumnsQuery()
    {
        $driver = new Driver();
        $queries = $driver->getQueryBuilder()->getAddColumnsQuery('foo', new Columns([
            'c1' => new Columns\IntColumn(),
            new Columns\StringColumn(['name' => 'c2'])
        ]));

        $this->assertInstanceOf(ExecutableInterface::class, $queries);
        $this->assertInstanceOf(Queries::class, $queries);

        $this->assertCount(2, $queries);

        $this->assertTrue($queries[0]->isString());
        $this->assertSame(
            "ALTER TABLE `foo` ADD COLUMN `c1` INTEGER",
            $queries[0]->string
        );

        $this->assertTrue($queries[1]->isString());
        $this->assertSame(
            "ALTER TABLE `foo` ADD COLUMN `c2` TEXT",
            $queries[1]->string
        );
    }

    public function testGetDropColumnQuery()
    {
        $driver = new Driver();

        $this->expectException(\BadMethodCallException::class);
        $driver->getQueryBuilder()->getDropColumnQuery('foo', 'bar');
    }

    public function testGetRenameColumnQuery()
    {
        $driver = new Driver();

        $this->expectException(\BadMethodCallException::class);
        $driver->getQueryBuilder()->getRenameColumnQuery('foo', 'bar', 'baz');
    }

}

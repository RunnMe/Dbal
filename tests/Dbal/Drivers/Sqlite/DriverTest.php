<?php

namespace Runn\tests\Dbal\Drivers\Sqlite\Driver;

use Runn\Dbal\Dbh;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Drivers\Sqlite\Driver;
use Runn\Dbal\Drivers\Sqlite\QueryBuilder;

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

        $this->assertSame(
            "SELECT count(*)>0\nFROM `sqlite_master` AS t1\nWHERE type=:type AND name=:name",
            $builder->makeQueryString($driver->getExistsTableQuery('foo'))
        );

        $this->assertSame(
            [
                ['name' => ':type', 'value' => 'table', 'type' => Dbh::DEFAULT_PARAM_TYPE],
                ['name' => ':name', 'value' => 'foo',   'type' => Dbh::DEFAULT_PARAM_TYPE],
            ],
            $driver->getExistsTableQuery('foo')->getParams()
        );
    }

}
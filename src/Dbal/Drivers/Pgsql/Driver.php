<?php

namespace Runn\Dbal\Drivers\Pgsql;

use Runn\Dbal\Column;
use Runn\Dbal\Columns;
use Runn\Dbal\Connection;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Drivers\Exception;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Index;
use Runn\Dbal\Indexes;
use Runn\Dbal\Queries;
use Runn\Dbal\Query;

/**
 * DBAL Postgres driver
 *
 * Class Driver
 * @package Runn\Dbal\Drivers\Pgsql
 */
class Driver
    extends \Runn\Dbal\Driver
{

    /**
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        $builder = new QueryBuilder;
        $builder->setDriver($this);
        return $builder;
    }

    /*
    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        $result = true;
        foreach ($indexes as $index) {
            $result = $result && $connection->execute(new Query('CREATE ' . $this->getIndexDDL($tableName, $index)));
        }
        return $result;
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        $result = true;
        foreach ($indexes as $index) {
            $order = $index->order ? $index->order . '.' : '';
            $result = $result && $connection->execute(new Query('DROP INDEX ' . $order . $index->name));
        }
        return $result;
    }
    */

}
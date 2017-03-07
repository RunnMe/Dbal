<?php

namespace Running\tests\Dbal\Drivers\WithoutDsn;

use Running\Dbal\Column;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;

class Driver
    implements DriverInterface
{

    public function getColumnDDL(Column $column): string
    {
    }

    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
    }

    public function existsTable(Connection $connection, $tableName)
    {
    }

    public function createTable(Connection $connection, string $tableName, Columns $columns = null, $indexes = [], $extensions = [])
    {
    }

    public function renameTable(Connection $connection, $tableName, $tableNewName)
    {
    }

    public function truncateTable(Connection $connection, $tableName)
    {
    }

    public function dropTable(Connection $connection, $tableName)
    {
    }

    public function addColumn(Connection $connection, $tableName, array $columns)
    {
    }

    public function dropColumn(Connection $connection, $tableName, array $columns)
    {
    }

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName)
    {
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
    }

    public function insert(Connection $connection, $tableName, array $data)
    {
    }

    public function findAllByQuery($class, $query, $params = [])
    {
    }

    public function findByQuery($class, $query, $params = [])
    {
    }

    public function findAll($class, $options = [])
    {
    }

    public function findAllByColumn($class, $column, $value, $options = [])
    {
    }

    public function findByColumn($class, $column, $value, $options = [])
    {
    }

    public function countAllByQuery($class, $query, $params = [])
    {
    }

    public function countAll($class, $options = [])
    {
    }

    public function countAllByColumn($class, $column, $value, $options = [])
    {
    }
}
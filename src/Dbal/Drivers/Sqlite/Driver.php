<?php

namespace Running\Dbal\Drivers\Sqlite;

use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;

/**
 * DBAL sqlite driver
 *
 * Class Sqlite
 * @package Running\Dbal\Drivers
 */
class Driver
    implements DriverInterface
{

    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        return new QueryBuilder;
    }

    public function createTable(Connection $connection, $tableName, $columns = [], $indexes = [], $extensions = [])
    {
        // TODO: Implement createTable() method.
    }

    public function existsTable(Connection $connection, $tableName)
    {
        // TODO: Implement existsTable() method.
    }

    public function renameTable(Connection $connection, $tableName, $tableNewName)
    {
        // TODO: Implement renameTable() method.
    }

    public function truncateTable(Connection $connection, $tableName)
    {
        // TODO: Implement truncateTable() method.
    }

    public function dropTable(Connection $connection, $tableName)
    {
        // TODO: Implement dropTable() method.
    }

    public function addColumn(Connection $connection, $tableName, array $columns)
    {
        // TODO: Implement addColumn() method.
    }

    public function dropColumn(Connection $connection, $tableName, array $columns)
    {
        // TODO: Implement dropColumn() method.
    }

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName)
    {
        // TODO: Implement renameColumn() method.
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement addIndex() method.
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement dropIndex() method.
    }

    public function insert(Connection $connection, $tableName, array $data)
    {
        // TODO: Implement insert() method.
    }

    public function findAllByQuery($class, $query, $params = [])
    {
        // TODO: Implement findAllByQuery() method.
    }

    public function findByQuery($class, $query, $params = [])
    {
        // TODO: Implement findByQuery() method.
    }

    public function findAll($class, $options = [])
    {
        // TODO: Implement findAll() method.
    }

    public function findAllByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement findAllByColumn() method.
    }

    public function findByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement findByColumn() method.
    }

    public function countAllByQuery($class, $query, $params = [])
    {
        // TODO: Implement countAllByQuery() method.
    }

    public function countAll($class, $options = [])
    {
        // TODO: Implement countAll() method.
    }

    public function countAllByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement countAllByColumn() method.
    }

}
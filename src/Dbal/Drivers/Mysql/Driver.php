<?php

namespace Running\Dbal\Drivers\Mysql;

use Running\Dbal\Column;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;
use Running\Dbal\Index;

/**
 * DBAL mysql driver
 *
 * Class Mysql
 * @package Running\Dbal\Drivers
 */
class Driver
    implements DriverInterface
{

    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        return new QueryBuilder;
    }

    /**
     * @param \Running\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {
        // TODO: Implement getColumnDDL() method.
    }

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {
        // TODO: Implement processValueAfterLoad() method.
    }

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {
        // TODO: Implement processValueBeforeSave() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
        // TODO: Implement existsTable() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @param \Running\Dbal\Columns $columns
     * @param array $indexes
     * @param array $extensions
     * @return mixed
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = []): bool
    {
        // TODO: Implement createTable() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool
    {
        // TODO: Implement renameTable() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {
        // TODO: Implement truncateTable() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function dropTable(Connection $connection, string $tableName): bool
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

    public function getIndexDDL(string $table, Index $index): string
    {
        // TODO: Implement getIndexDDL() method.
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement addIndex() method.
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement dropIndex() method.
    }
}

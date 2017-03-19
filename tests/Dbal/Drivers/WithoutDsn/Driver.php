<?php

namespace Running\tests\Dbal\Drivers\WithoutDsn;

use Running\Dbal\Column;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;
use Running\Dbal\Index;

class Driver
    implements DriverInterface
{

    /**
     * @return \Running\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
    }

    /**
     * @param \Running\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {
    }

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {
    }

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
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
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool
    {
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function dropTable(Connection $connection, string $tableName): bool
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

    public function getIndexDDL(string $table, Index $index): string
    {
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
    }

}
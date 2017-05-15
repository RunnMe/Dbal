<?php

namespace Runn\tests\Dbal\Drivers\WithoutDsn;

use Runn\Dbal\Column;
use Runn\Dbal\Columns;
use Runn\Dbal\Connection;
use Runn\Dbal\DriverInterface;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Index;

class Driver
    implements DriverInterface
{

    /**
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @param array $indexes
     * @param array $extensions
     * @return mixed
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = []): bool
    {
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool
    {
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {
    }

    /**
     * @param \Runn\Dbal\Connection $connection
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
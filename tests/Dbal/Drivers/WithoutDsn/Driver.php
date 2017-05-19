<?php

namespace Runn\tests\Dbal\Drivers\WithoutDsn;

use Runn\Dbal\Column;
use Runn\Dbal\Columns;
use Runn\Dbal\Connection;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\Index;
use Runn\Dbal\Indexes;

class Driver
    extends \Runn\Dbal\Driver
{

    /**
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface
    {}

    /**
     * @param \Runn\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {}

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {}

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {}

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {}

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return bool
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): bool
    {}

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool
    {}

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {}

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function dropTable(Connection $connection, string $tableName): bool
    {}

    public function addColumn(Connection $connection, $tableName, array $columns)
    {}

    public function dropColumn(Connection $connection, $tableName, array $columns)
    {}

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName)
    {}

    public function getIndexDDL(string $table, Index $index): string
    {}

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {}

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {}

}
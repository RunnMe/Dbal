<?php

namespace Runn\tests\Dbal\Drivers\WithoutDsn;

use Runn\Dbal\Column;
use Runn\Dbal\Columns;
use Runn\Dbal\Connection;
use Runn\Dbal\DriverQueryBuilderInterface;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Index;
use Runn\Dbal\Indexes;
use Runn\Dbal\Query;

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
     * @param \Runn\Dbal\Index $index
     * @return string
     */
    public function getIndexDDL(Index $index): string
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
     * @param string $tableName
     * @return \Runn\Dbal\Query
     */
    public function getExistsTableQuery(string $tableName): Query
    {}

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getCreateTableQuery(string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): ExecutableInterface
    {}

    /**
     * @param string $tableOldName
     * @param string $tableNewName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getRenameTableQuery(string $tableOldName, string $tableNewName): ExecutableInterface
    {}

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getTruncateTableQuery(string $tableName): ExecutableInterface
    {}

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getDropTableQuery(string $tableName): ExecutableInterface
    {}

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Column $column
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getAddColumnQuery(string $tableName, Column $column): ExecutableInterface
    {}

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getAddColumnsQuery(string $tableName, Columns $columns): ExecutableInterface
    {}

    public function dropColumn(Connection $connection, $tableName, array $columns)
    {}

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName)
    {}

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {}

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {}

}
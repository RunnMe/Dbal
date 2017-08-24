<?php

namespace Runn\Dbal;

/**
 * Interface DriverQueryBuilderInterface
 * @package Runn\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverQueryBuilderInterface
    extends DriverAwareInterface
{

    /**
     * @param string $name
     * @return string
     */
    public function quoteName(string $name): string;

    /**
     * @param \Runn\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string;

    /**
     * @param \Runn\Dbal\Index $index
     * @return string
     */
    public function getIndexDDL(Index $index): string;

    /**
     * @param string $tableName
     * @return \Runn\Dbal\Query
     */
    public function getExistsTableQuery(string $tableName): Query;

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getCreateTableQuery(string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): ExecutableInterface;

    /**
     * @param string $tableOldName
     * @param string $tableNewName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getRenameTableQuery(string $tableOldName, string $tableNewName): ExecutableInterface;

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getTruncateTableQuery(string $tableName): ExecutableInterface;

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getDropTableQuery(string $tableName): ExecutableInterface;

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Column $column
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getAddColumnQuery(string $tableName, Column $column): ExecutableInterface;

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getAddColumnsQuery(string $tableName, Columns $columns): ExecutableInterface;

    /**
     * @param string $tableName
     * @param string $columnName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getDropColumnQuery(string $tableName, string $columnName): ExecutableInterface;

    /**
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getRenameColumnQuery(string $tableName, string $oldColumnName, string $newColumnName): ExecutableInterface;

    /**
     * @param \Runn\Dbal\Query $query
     * @return string
     */
    public function makeQueryString(Query $query) : string;

}
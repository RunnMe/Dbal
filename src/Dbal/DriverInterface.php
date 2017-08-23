<?php

namespace Runn\Dbal;

/**
 * DB Driver common interface
 *
 * Interface DriverInterface
 * @package Runn\Dbal
 *
 * @codeCoverageIgnore
 */
interface DriverInterface
{

    /**
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface;

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
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value);

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value);

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

    public function addIndex(Connection $connection, $tableName, array $indexes);

    public function dropIndex(Connection $connection, $tableName, array $indexes);

    /*
    public function insert(Connection $connection, $tableName, array $data);

    public function findAllByQuery($class, $query, $params = []);

    public function findByQuery($class, $query, $params = []);

    public function findAll($class, $options = []);

    public function findAllByColumn($class, $column, $value, $options = []);

    public function findByColumn($class, $column, $value, $options = []);

    public function countAllByQuery($class, $query, $params = []);

    public function countAll($class, $options = []);

    public function countAllByColumn($class, $column, $value, $options = []);
    */

    /*
    public function save(Model $model);

    public function delete(Model $model);
    */

}
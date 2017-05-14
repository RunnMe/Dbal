<?php

namespace Runn\Dbal;

/**
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
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @param array $indexes
     * @param array $extensions
     * @return mixed
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = []): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function truncateTable(Connection $connection, string $tableName): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function dropTable(Connection $connection, string $tableName): bool;

    public function addColumn(Connection $connection, $tableName, array $columns);

    public function dropColumn(Connection $connection, $tableName, array $columns);

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName);

    public function getIndexDDL(string $table, Index $index): string;

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
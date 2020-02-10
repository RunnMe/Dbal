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
     * Returns DSN class name for this driver
     *
     * @return string
     */
    public static function getDsnClassName(): string;

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
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     */
    public function existsTable(Connection $connection, string $tableName): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return bool
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableOldName
     * @param string $tableNewName
     * @return bool
     */
    public function renameTable(Connection $connection, string $tableOldName, string $tableNewName): bool;

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

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Column $column
     * @return bool
     */
    public function addColumn(Connection $connection, string $tableName, Column $column): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @return bool
     */
    public function addColumns(Connection $connection, string $tableName, Columns $columns): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @return bool
     */
    public function renameColumn(Connection $connection, string $tableName, string $oldColumnName, string $newColumnName): bool;

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function dropColumn(Connection $connection, string $tableName, string $columnName): bool;

    /*
    public function addIndex(Connection $connection, $tableName, array $indexes);

    public function dropIndex(Connection $connection, $tableName, array $indexes);

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
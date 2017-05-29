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
     * @return \Runn\Dbal\Query
     */
    public function getCreateTableQuery(string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): Query;

    /**
     * @param string $tableOldName
     * @param string $tableNewName
     * @return \Runn\Dbal\Query
     */
    public function getRenameTableQuery(string $tableOldName, string $tableNewName): Query;

    /**
     * @param string $tableName
     * @return \Runn\Dbal\Query
     */
    public function getTruncateTableQuery(string $tableName): Query;

    /**
     * @param string $tableName
     * @return \Runn\Dbal\Query
     */
    public function getDropTableQuery(string $tableName): Query;

    public function addColumn(Connection $connection, $tableName, array $columns);

    public function dropColumn(Connection $connection, $tableName, array $columns);

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName);

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
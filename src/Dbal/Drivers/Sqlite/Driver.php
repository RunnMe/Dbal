<?php

namespace Running\Dbal\Drivers\Sqlite;

use Running\Dbal\Column;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;
use Running\Dbal\Index;
use Running\Dbal\Query;

/**
 * DBAL sqlite driver
 *
 * Class Sqlite
 * @package Running\Dbal\Drivers
 */
class Driver
    implements DriverInterface
{

    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        return new QueryBuilder;
    }

    public function getIndexDDL(Index $index): string
    {
        switch (get_class($index)) {
            case \Running\Dbal\Indexes\UniqueIndex::class:
                $ddl = 'UNIQUE INDEX ';
                break;
            case \Running\Dbal\Indexes\SimpleIndex::class:
            default:
                $ddl = 'INDEX ';
        }
        $indexName = $index->name ?? implode('_', $index->columns) . '_idx';
        $ddl .= $index->schema ? $index->schema . '.': '';
        $ddl .= $indexName . ' ON ' . $index->table . ' ';
        $ddl .= '(' . implode(', ', $index->columns) . ')';
        return $ddl;
    }
    
    public function getColumnDDL(Column $column): string
    {
        switch (get_class($column)) {
            case \Running\Dbal\Columns\SerialColumn::class:
            case \Running\Dbal\Columns\PkColumn::class:
                $ddl =  'INTEGER PRIMARY KEY AUTOINCREMENT';
                break;
            case \Running\Dbal\Columns\LinkColumn::class:
                $ddl = 'INTEGER DEFAULT NULL';
                break;
            case \Running\Dbal\Columns\BooleanColumn::class:
                $ddl = 'INTEGER';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : (int)(bool)$column->default) : null;
                break;
            case \Running\Dbal\Columns\IntColumn::class:
                $ddl = 'INTEGER';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Running\Dbal\Columns\FloatColumn::class:
                $ddl = 'REAL';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Running\Dbal\Columns\CharColumn::class:
            case \Running\Dbal\Columns\StringColumn::class:
            case \Running\Dbal\Columns\TimeColumn::class:
            case \Running\Dbal\Columns\DateColumn::class:
            case \Running\Dbal\Columns\DateTimeColumn::class:
                $ddl = 'TEXT';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : "'" . $column->default . "'") : null;
                break;
            default:
                $ddl = $column->getColumnDdlByDriver($this);
                break;
        }

        if (isset($default)) {
            $ddl .= ' DEFAULT ' . $default;
        }

        return $ddl;
    }

    public function existsTable(Connection $connection, $tableName)
    {
        $query = (new Query())->select('count(*)')->from('sqlite_master')->where('type=:type AND name=:name')->params([
            ':type'=>'table',
            ':name'=>$tableName,
        ]);
        return 0 != $connection->query($query)->fetchScalar();
    }

    protected function createTableDdl(string $tableName, Columns $columns, $indexes = [], $extensions = [])
    {
        $sql = 'CREATE TABLE ' . $this->getQueryBuilder()->quoteName($tableName) . "\n";

        $columnsDDL = [];

        foreach ($columns as $name => $column) {
            $columnsDDL[] = $this->getQueryBuilder()->quoteName($name) . ' ' . $this->getColumnDDL($column);
        }

        $sql .=
            "(\n" .
                implode(",\n", array_unique($columnsDDL)) .
            "\n)";
        return $sql;
    }

    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = [])
    {
        return $connection->execute(new Query($this->createTableDdl($tableName, $columns)));
    }

    public function renameTable(Connection $connection, $oldTableName, $newTableName)
    {
        $query = new Query('ALTER TABLE ' . $this->getQueryBuilder()->quoteName($oldTableName) . ' RENAME TO ' . $this->getQueryBuilder()->quoteName($newTableName));
        return $connection->execute($query);
    }

    public function truncateTable(Connection $connection, $tableName)
    {
        // TODO: Implement truncateTable() method.
    }

    public function dropTable(Connection $connection, $tableName)
    {
        $query = new Query('DROP TABLE' . $this->getQueryBuilder()->quoteName($tableName));
        return $connection->execute($query);
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

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement addIndex() method.
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement dropIndex() method.
    }

    public function insert(Connection $connection, $tableName, array $data)
    {
        // TODO: Implement insert() method.
    }

    public function findAllByQuery($class, $query, $params = [])
    {
        // TODO: Implement findAllByQuery() method.
    }

    public function findByQuery($class, $query, $params = [])
    {
        // TODO: Implement findByQuery() method.
    }

    public function findAll($class, $options = [])
    {
        // TODO: Implement findAll() method.
    }

    public function findAllByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement findAllByColumn() method.
    }

    public function findByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement findByColumn() method.
    }

    public function countAllByQuery($class, $query, $params = [])
    {
        // TODO: Implement countAllByQuery() method.
    }

    public function countAll($class, $options = [])
    {
        // TODO: Implement countAll() method.
    }

    public function countAllByColumn($class, $column, $value, $options = [])
    {
        // TODO: Implement countAllByColumn() method.
    }

}
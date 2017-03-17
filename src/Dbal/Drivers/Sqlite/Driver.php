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

    public function getColumnDDL(Column $column): string
    {
        switch (get_class($column)) {
            case \Running\Dbal\Columns\SerialColumn::class:
            case \Running\Dbal\Columns\PkColumn::class:
                $ddl = 'INTEGER PRIMARY KEY AUTOINCREMENT';
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
            case \Running\Dbal\Columns\TextColumn::class:
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

    public function processValueAfterLoad(Column $column, $value)
    {
        return $value;
    }

    public function processValueBeforeSave(Column $column, $value)
    {
        return $value;
    }

    public function getIndexDDL(string $table, Index $index): string
    {
        switch (get_class($index)) {
            case \Running\Dbal\Indexes\UniqueIndex::class:
                $ddl = 'UNIQUE INDEX ';
                break;
            case \Running\Dbal\Indexes\SimpleIndex::class:
                $ddl = 'INDEX ';
                break;
            default:
                return $index->getIndexDdlByDriver($this);
        }

        $columns = [];
        $columnNames = [];
        foreach ($index->columns as $column) {
            preg_match('~^([\S]+)(\s+(asc|desc))?~i', $column, $m);
            $columnName = trim($m[1], '`" ');
            $columnNames[] = $columnName;
            $columns[] = $this->getQueryBuilder()->quoteName($columnName) . (!empty($m[3]) ? ' ' . strtoupper($m[3]) : '');
        }

        $index->name  = $index->name ?? implode('_', $columnNames) . '_idx';
        $index->table = $table;

        $ddl .= $this->getQueryBuilder()->quoteName($index->name) . ' ON ' . $this->getQueryBuilder()->quoteName($table);
        $ddl .= ' (' . implode(', ', $columns) . ')';

        return $ddl;
    }

    public function existsTable(Connection $connection, string $tableName): bool
    {
        $query = (new Query())->select('count(*)')->from('sqlite_master')->where('type=:type AND name=:name')->params([
            ':type' => 'table',
            ':name' => $tableName,
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

    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = []): bool
    {
        return $connection->execute(new Query($this->createTableDdl($tableName, $columns)));
    }

    public function renameTable(Connection $connection, string $oldTableName, string $newTableName): bool
    {
        $query = new Query('ALTER TABLE ' . $this->getQueryBuilder()->quoteName($oldTableName) . ' RENAME TO ' . $this->getQueryBuilder()->quoteName($newTableName));
        return $connection->execute($query);
    }

    public function truncateTable(Connection $connection, string $tableName): bool
    {
        $query = new Query('DELETE FROM ' . $this->getQueryBuilder()->quoteName($tableName));
        return $connection->execute($query);
    }

    public function dropTable(Connection $connection, string $tableName): bool
    {
        $query = new Query('DROP TABLE ' . $this->getQueryBuilder()->quoteName($tableName));
        return $connection->execute($query);
    }

    public function addColumn(Connection $connection, $tableName, string $columnName, Column $column)
    {
        $query = new Query(
            'ALTER TABLE ' . $this->getQueryBuilder()->quoteName($tableName) .
            ' ADD COLUMN ' . $this->getQueryBuilder()->quoteName($columnName) .
            ' ' . $this->getColumnDDL($column)
        );
        return $connection->execute($query);
    }

    public function addColumns(Connection $connection, $tableName, Columns $columns)
    {
        $result = true;
        foreach ($columns as $colName => $column) {
            $result = $result && $this->addColumn($connection, $tableName, $colName, $column);
        }
        return $result;
    }

    public function dropColumn(Connection $connection, $tableName, string $columnName)
    {
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $newColumns = [];
        foreach ($columnsInfo as $columnInfo) {
            $name = $columnInfo['name'];
            if ($name != $columnName) {
                $newColumns[] = $this->getQueryBuilder()->quoteName($columnInfo['name']);
            }
        }
        $tmpTable = $tableName . '_backup';
        $result = true;
        $result = $result && $connection->transactionBegin();
        if ($result) {
            $query = new Query(
                'CREATE TABLE ' . $this->getQueryBuilder()->quoteName($tmpTable) .
                ' AS SELECT ' . implode(', ', $newColumns) .
                'FROM ' . $this->getQueryBuilder()->quoteName($tableName)
            );
            $result = $result && $connection->execute($query);
            $result = $result && $this->dropTable($connection, $tableName);
            $result = $result && $this->renameTable($connection, $tmpTable, $tableName);
            $result = $result && $connection->transactionCommit();
        }
        return $result;
    }

    public function dropColumns(Connection $connection, $tableName, array $columns)
    {
        $result = true;
        foreach ($columns as $columnName) {
            $result = $result && $this->dropColumn($connection, $tableName, $columnName);
        }
        return $result;
    }

    public function renameColumn(Connection $connection, $tableName, $oldName, $newName)
    {
        $columnsInfo = $connection->query(new Query('PRAGMA table_info(foo)'))->fetchAll();
        $newColumns = [];
        foreach ($columnsInfo as $columnInfo) {
            $name = $columnInfo['name'];
            if ($name != $oldName) {
                $newColumns[] = $this->getQueryBuilder()->quoteName($columnInfo['name']);
            } else {
                $newColumns[] = $this->getQueryBuilder()->quoteName($oldName) .
                    ' AS ' . $this->getQueryBuilder()->quoteName($newName);
            }
        }
        $tmpTable = $tableName . '_backup';
        $result = true;
        $result = $result && $connection->transactionBegin();
        if ($result) {
            $query = new Query(
                'CREATE TABLE ' . $this->getQueryBuilder()->quoteName($tmpTable) .
                ' AS SELECT ' . implode(', ', $newColumns) .
                ' FROM ' . $this->getQueryBuilder()->quoteName($tableName)
            );
            $result = $result && $connection->execute($query);
            $result = $result && $this->dropTable($connection, $tableName);
            $result = $result && $this->renameTable($connection, $tmpTable, $tableName);
            $result = $result && $connection->transactionCommit();
        }
        return $result;
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        $result = true;
        foreach ($indexes as $index) {
            $result = $result && $connection->execute(new Query('CREATE ' . $this->getIndexDDL($tableName, $index)));
        }
        return $result;
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        $result = true;
        foreach ($indexes as $index) {
            $order = $index->order ? $index->order . '.' : '';
            $result = $result && $connection->execute(new Query('DROP INDEX ' . $order . $index->name));
        }
        return $result;
    }

    public function insert(Connection $connection, $tableName, array $data)
    {
        $queryValues = [];
        $executeValues = [];
        foreach ($data as $key => $value) {
            $queryValues[$key] = ':' . $key;
            $executeValues[':' . $key] = $value;
        }
        $query = new Query();
        $query->insert()->table($tableName)->values($queryValues);
        $connection->execute($query, $executeValues);
        return $connection->lastInsertId();
    }
}

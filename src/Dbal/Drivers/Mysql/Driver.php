<?php

namespace Running\Dbal\Drivers\Mysql;

use Running\Dbal\Column;
use Running\Dbal\Columns;
use Running\Dbal\Connection;
use Running\Dbal\DriverInterface;
use Running\Dbal\DriverQueryBuilderInterface;
use Running\Dbal\Drivers\Exception;
use Running\Dbal\Index;
use Running\Dbal\Query;
use Running\Sanitization\Sanitizers\Date;
use Running\Sanitization\Sanitizers\DateTime;

/**
 * DBAL mysql driver
 *
 * Class Mysql
 * @package Running\Dbal\Drivers
 */
class Driver
    implements DriverInterface
{

    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        return new QueryBuilder;
    }

    /**
     * @param \Running\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {
        switch (get_class($column)) {
            case \Running\Dbal\Columns\SerialColumn::class:
                $ddl = 'SERIAL';
                break;
            case \Running\Dbal\Columns\PkColumn::class:
                if (isset($column->autoincrement) && false == $column->autoincrement) {
                    $ddl = 'BIGINT UNSIGNED NOT NULL';
                } else {
                    $ddl = 'SERIAL';
                }
                break;
            case \Running\Dbal\Columns\LinkColumn::class:
                $ddl = 'BIGINT UNSIGNED NULL DEFAULT NULL';
                break;
            case \Running\Dbal\Columns\BooleanColumn::class:
                $ddl = 'BOOLEAN';
                $default = isset($column->default) ?
                    (null === $column->default ? 'NULL' : (bool)$column->default) :
                    null;
                break;
            case \Running\Dbal\Columns\IntColumn::class:
                $ddl = 'INTEGER';
                $ddl .= isset($column->dimension) ? '(' . $column->dimension . ')' : '';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Running\Dbal\Columns\FloatColumn::class:
                $ddl = 'REAL';
                $ddl .= isset($column->dimension) ? '(' . $column->dimension . ')' : '';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Running\Dbal\Columns\CharColumn::class:
                $ddl = 'CHAR';
                $ddl .= isset($column->length) ? '(' . (int)$column->length . ')' : '(255)';
                $default = isset($column->default) ?
                    (null === $column->default ? 'NULL' : "'" . $column->default . "'") :
                    null;
                break;
            case \Running\Dbal\Columns\StringColumn::class:
                $ddl = 'VARCHAR';
                $ddl .= isset($column->length) ? '(' . (int)$column->length . ')' : '(255)';
                $default = isset($column->default) ?
                    (null === $column->default ? 'NULL' : "'" . $column->default . "'") :
                    null;
                break;
            case \Running\Dbal\Columns\TextColumn::class:
                $ddl = 'TEXT';
                $default = isset($column->default) ?
                    (null === $column->default ? 'NULL' : "'" . $column->default . "'") :
                    null;
                break;
            case \Running\Dbal\Columns\TimeColumn::class:
                $ddl = 'TIME';
                if (isset($column->default)) {
                    $default = 'NULL';
                    if (!is_null($column->default)) {
                        try {
                            $default = "'" . (new DateTime())->sanitize($column->default)->format('H:i:s') . "'";
                        } catch (\Exception $e) {
                        }
                    }
                }
                break;
            case \Running\Dbal\Columns\DateColumn::class:
                $ddl = 'DATE';
                if (isset($column->default)) {
                    $default = 'NULL';
                    if (!is_null($column->default)) {
                        try {
                            $default = "'" . (new Date())->sanitize($column->default)->format('Y-m-d') . "'";
                        } catch (\Exception $e) {
                        }
                    }
                }
                break;
            case \Running\Dbal\Columns\DateTimeColumn::class:
                $ddl = 'DATETIME';
                if (isset($column->default)) {
                    $default = 'NULL';
                    if (!is_null($column->default)) {
                        try {
                            $default = "'" . (new DateTime())->sanitize($column->default)->format('Y-m-d H:i:s') . "'";
                        } catch (\Exception $e) {
                        }
                    }
                }
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

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {
        // TODO: Implement processValueAfterLoad() method.
    }

    /**
     * @param \Running\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {
        // TODO: Implement processValueBeforeSave() method.
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return mixed
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
        $query = (new Query('SHOW TABLES LIKE :name'))->params([':name' => $tableName,]);
        return $tableName === $connection->query($query)->fetchScalar();
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

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @param \Running\Dbal\Columns $columns
     * @param array $indexes
     * @param array $extensions
     * @return mixed
     * @throws \Running\Dbal\Drivers\Exception
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns, $indexes = [], $extensions = []): bool
    {
        return $connection->execute(new Query($this->createTableDdl($tableName, $columns)));
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @param string $tableNewName
     * @return bool
     * @throws \Running\Dbal\Drivers\Exception
     */
    public function renameTable(Connection $connection, string $tableName, string $tableNewName): bool
    {
        $sql = 'RENAME TABLE ' .
            $this->getQueryBuilder()->quoteName($tableName) .
            ' TO ' .
            $this->getQueryBuilder()->quoteName($tableNewName);
        try {
            return $connection->execute(new Query($sql));
        } catch (\PDOException $e) {
            throw new Exception('Error renameTable: ' . $e->getMessage());
        }
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     * @throws \Running\Dbal\Drivers\Exception
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {
        $sql = 'TRUNCATE TABLE ' . $this->getQueryBuilder()->quoteName($tableName);
        try {
            return $connection->execute(new Query($sql));
        } catch (\PDOException $e) {
            throw new Exception('Error truncateTable: ' . $e->getMessage());
        }
    }

    /**
     * @param \Running\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     * @throws \Running\Dbal\Drivers\Exception
     */
    public function dropTable(Connection $connection, string $tableName): bool
    {
        $sql = 'DROP TABLE ' . $this->getQueryBuilder()->quoteName($tableName);
        try {
            return $connection->execute(new Query($sql));
        } catch (\PDOException $e) {
            throw new Exception('Error dropTable: ' . $e->getMessage());
        }
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

    public function getIndexDDL(string $table, Index $index): string
    {
        switch (get_class($index)) {
            case \Running\Dbal\Indexes\FulltextIndex::class:
                $ddl = 'FULLTEXT INDEX ';
                break;
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
            $columnNames[] = explode('(', $columnName)[0];
            $columns[] = $this->getQueryBuilder()->quoteName($columnName) . (!empty($m[3]) ? ' ' . strtoupper($m[3]) : '');
        }

        $index->name = $index->name ?? implode('_', $columnNames) . '_idx';
        $index->table = $table;

        $ddl .= $this->getQueryBuilder()->quoteName($index->name) . ' ON ' . $this->getQueryBuilder()->quoteName($table);
        $ddl .= ' (' . implode(', ', $columns) . ')';

        return $ddl;
    }

    public function addIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement addIndex() method.
    }

    public function dropIndex(Connection $connection, $tableName, array $indexes)
    {
        // TODO: Implement dropIndex() method.
    }
}

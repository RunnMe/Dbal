<?php

namespace Runn\Dbal\Drivers\Sqlite;

use Runn\Dbal\Column;
use Runn\Dbal\Columns;
use Runn\Dbal\DriverQueryBuilder;
use Runn\Dbal\Drivers\Exception;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Index;
use Runn\Dbal\Indexes;
use Runn\Dbal\Queries;
use Runn\Dbal\Query;

/**
 * Class QueryBuilder
 * @package Runn\Dbal\Drivers\Sqlite\Sqlite
 *
 * @implements \Runn\Dbal\DriverQueryBuilderInterface
 */
class QueryBuilder
    extends DriverQueryBuilder
{

    /**
     * @param string $name
     * @return string
     */
    public function quoteName(string $name): string
    {
        $parts = explode('.', $name);
        $lastIndex = count($parts) - 1;

        $selectNoQouteTemplate = '~select|count|avg|group_concat|min|max|sum~i';

        foreach ($parts as $index => &$part) {
            if ('*' == $part) {
                continue;
            }
            if (false !== strpos($part, '(') || false !== strpos($part, ')')) {
                continue;
            }
            if (
                (
                    $index == $lastIndex
                    ||
                    !preg_match('~^(t|j)[\d]+$~', $part)
                ) &&
                !preg_match($selectNoQouteTemplate, $part)
            ) {
                $part = '`' . $part . '`';
            }
        }
        return implode('.', $parts);
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @return string
     */
    public function getColumnDDL(Column $column): string
    {
        switch (get_class($column)) {
            case \Runn\Dbal\Columns\SerialColumn::class:
                $ddl =  'INTEGER AUTOINCREMENT';
                break;
            case \Runn\Dbal\Columns\PkColumn::class:
                $ddl =  'INTEGER PRIMARY KEY AUTOINCREMENT';
                break;
            case \Runn\Dbal\Columns\LinkColumn::class:
                $ddl = 'INTEGER DEFAULT NULL';
                break;
            case \Runn\Dbal\Columns\BooleanColumn::class:
                $ddl = 'INTEGER';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : (int)(bool)$column->default) : null;
                break;
            case \Runn\Dbal\Columns\IntColumn::class:
                $ddl = 'INTEGER';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Runn\Dbal\Columns\FloatColumn::class:
                $ddl = 'REAL';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : $column->default) : null;
                break;
            case \Runn\Dbal\Columns\CharColumn::class:
            case \Runn\Dbal\Columns\StringColumn::class:
            case \Runn\Dbal\Columns\TimeColumn::class:
            case \Runn\Dbal\Columns\DateColumn::class:
            case \Runn\Dbal\Columns\DateTimeColumn::class:
                $ddl = 'TEXT';
                $default = isset($column->default) ? (null === $column->default ? 'NULL' : "'" . $column->default . "'") : null;
                break;
            default:
                $ddl = $column->getColumnDdlByDriver($this->getDriver());
                break;
        }

        if (isset($default)) {
            $ddl .= ' DEFAULT ' . $default;
        }

        return $ddl;
    }

    /**
     * @param \Runn\Dbal\Index $index
     * @return string
     */
    public function getIndexDDL(Index $index): string
    {
        switch (get_class($index)) {
            case \Runn\Dbal\Indexes\UniqueIndex::class:
                $ddl = 'UNIQUE INDEX ';
                break;
            case \Runn\Dbal\Indexes\SimpleIndex::class:
                $ddl = 'INDEX ';
                break;
            default:
                return $index->getIndexDdlByDriver($this->getDriver());
        }

        // @todo: check required "columns" and "table"

        $columns = [];
        $columnNames = [];
        foreach ($index->columns as $column) {
            preg_match('~^([\S]+)(\s+(asc|desc))?~i', $column, $m);
            $columnName = trim($m[1], '`" ');
            $columnNames[] = $columnName;
            $columns[] = $this->quoteName($columnName) . (!empty($m[3]) ? ' ' . strtoupper($m[3]) : '');
        }

        $index->name  = $index->name ?? implode('_', $columnNames) . '_idx';

        $ddl .= $this->quoteName($index->name) . ' ON ' . $this->quoteName($index->table);
        $ddl .= ' (' . implode(', ', $columns) . ')';

        return $ddl;
    }

    /**
     * @param string $tableName
     * @return \Runn\Dbal\Query
     */
    public function getExistsTableQuery(string $tableName): Query
    {
        return (new Query())->select('count(*)>0')->from('sqlite_master')->where('type=:type AND name=:name')->params([
            ':type' => 'table',
            ':name' => $tableName,
        ]);
    }

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getCreateTableQuery(string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): ExecutableInterface
    {
        if (empty($tableName)) {
            throw new Exception('Empty table name');
        }

        if (null == $columns || $columns->empty()) {
            throw new Exception('Empty columns list');
        }

        $sql = 'CREATE TABLE ' . $this->quoteName($tableName) . "\n";

        $columnsDDL = [];

        foreach ($columns as $name => $column) {
            $name = $column->name ?? $name;
            if (empty($name) || is_numeric($name)) {
                throw new Exception('Empty column name');
            }
            $columnsDDL[] = $this->quoteName($name) . ' ' . $this->getColumnDDL($column);
        }

        $sql .=
            "(\n" .
            implode(",\n", array_unique($columnsDDL)) .
            "\n)";
        return new Query($sql);
    }

    /**
     * @param string $tableOldName
     * @param string $tableNewName
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getRenameTableQuery(string $tableOldName, string $tableNewName): ExecutableInterface
    {
        if (empty($tableOldName)) {
            throw new Exception('Empty old table name');
        }

        if (empty($tableNewName)) {
            throw new Exception('Empty new table name');
        }

        return new Query('ALTER TABLE ' . $this->quoteName($tableOldName) . ' RENAME TO ' . $this->quoteName($tableNewName));
    }

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getTruncateTableQuery(string $tableName): ExecutableInterface
    {
        if (empty($tableName)) {
            throw new Exception('Empty table name');
        }

        return new Queries([
            (new Query)->delete()->from($tableName),
            (new Query)->update('SQLITE_SEQUENCE')->set('seq', 0)->where('name=:name')->param(':name', $tableName),
        ]);
    }

    /**
     * @param string $tableName
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getDropTableQuery(string $tableName): ExecutableInterface
    {
        if (empty($tableName)) {
            throw new Exception('Empty table name');
        }

        return new Query('DROP TABLE ' . $this->quoteName($tableName));
    }

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Column $column
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getAddColumnQuery(string $tableName, Column $column): ExecutableInterface
    {
        if (empty($tableName)) {
            throw new Exception('Empty table name');
        }

        if (empty($column->name)) {
            throw new Exception('Empty column name');
        }

        $columnDDL = $this->quoteName($column->name) . ' ' . $this->getColumnDDL($column);
        return new Query('ALTER TABLE ' . $this->quoteName($tableName) . ' ADD COLUMN ' . $columnDDL);
    }

    /**
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \Runn\Dbal\Drivers\Exception
     */
    public function getAddColumnsQuery(string $tableName, Columns $columns): ExecutableInterface
    {
        if (empty($tableName)) {
            throw new Exception('Empty table name');
        }

        $ret = new Queries;

        foreach ($columns as $name => $column) {
            $name = $column->name ?? $name;
            if (empty($name) || is_numeric($name)) {
                throw new Exception('Empty column name');
            }
            $column->name = $name;
            $ret[] = $this->getAddColumnQuery($tableName, $column);
        }

        return $ret;
    }

    /**
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @return \Runn\Dbal\ExecutableInterface
     */
    public function getRenameColumnQuery(string $tableName, string $oldColumnName, string $newColumnName): ExecutableInterface
    {
        throw new \BadMethodCallException;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @return \Runn\Dbal\ExecutableInterface
     * @throws \BadMethodCallException
     */
    public function getDropColumnQuery(string $tableName, string $columnName): ExecutableInterface
    {
        throw new \BadMethodCallException;
    }

    protected function getTableNameAlias($name, $type, $counter)
    {
        $typeAliases = ['main' => 't', 'join' => 'j'];
        return $this->quoteName($name) . ' AS ' . $typeAliases[$type] . $counter;
    }

    public function makeQueryString(Query $query) : string
    {
        if (!empty($query->string)) {
            return $query->string;
        }
        switch ($query->action) {
            case 'select':
                return $this->makeQueryStringSelect($query);
            case 'insert':
                return $this->makeQueryStringInsert($query);
            case 'update':
                return $this->makeQueryStringUpdate($query);
            case 'delete':
                return $this->makeQueryStringDelete($query);
        }
        throw new Exception('Invalid query action');
    }

    protected function makeQueryStringSelect(Query $query)
    {
        if (empty($query->columns) || empty($query->tables)) {
            throw new Exception("SELECT statement must have both 'columns' and 'tables' parts");
        }

        $sql = '';

        if (!empty($query->with)) {
            $sql .= 'WITH ' . implode(', ', $query->with);
            $sql .= "\n";
        }

        $sql .= 'SELECT ';
        if ($query->columns == ['*']) {
            $sql .= '*';
        } else {
            $select = array_map([$this, 'quoteName'], $query->columns);
            $sql .= implode(', ', $select);
        }
        $sql .= "\n";

        $sql .= 'FROM ';
        $driver = $this;
        $from = array_map(function ($x) use ($driver) {
            static $c = 1;
            return $this->getTableNameAlias($x, 'main', $c++);
        }, $query->tables);
        $sql .= implode(', ', $from);
        $sql .= "\n";

        if (!empty($query->joins)) {
            $driver = $this;
            $joins = array_map(function ($x) use ($driver) {
                static $c = 1;
                $table = empty($x['alias']) ? $this->getTableNameAlias($x['table'], 'join', $c++) : $this->quoteName($x['table']) . ' AS ' . $this->quoteName($x['alias']);
                $x['table'] = $table;
                return $x;
            }, $query->joins);
            $j = [];
            foreach ($joins as $join) {
                switch ($join['type']) {
                    case 'inner':
                        $ret = 'INNER JOIN';
                        break;
                    case 'left':
                        $ret = 'LEFT JOIN';
                        break;
                    case 'cross':
                        $ret = 'CROSS JOIN';
                        break;
                    default:
                        $ret = 'INNER JOIN';
                }
                $j[] = $ret . ' ' . $join['table'] . ' ON ' . $join['on'];
            };
            $sql .= implode("\n", $j);
            $sql .= "\n";
        }

        if (!empty($query->where)) {
            $sql .= 'WHERE ' . $query->where;
            $sql .= "\n";
        }

        if (!empty($query->group)) {
            $sql .= 'GROUP BY ' . implode(', ', $query->group);
            $sql .= "\n";
        }

        if (!empty($query->having)) {
            $sql .= 'HAVING ' . $query->having;
            $sql .= "\n";
        }

        if (!empty($query->order)) {
            $sql .= 'ORDER BY ' . implode(', ', $query->order);
            $sql .= "\n";
        }

        if (!empty($query->limit)) {
            $sql .= 'LIMIT ' . $query->limit;
            $sql .= "\n";
            if (!empty($query->offset)) {
                $sql .= 'OFFSET ' . $query->offset;
                $sql .= "\n";
            }
        }

        $sql = preg_replace('~\n$~', '', $sql);
        return $sql;
    }

    protected function makeQueryStringInsert(Query $query)
    {
        if (empty($query->tables) || empty($query->values)) {
            throw new Exception("INSERT statement must have both 'tables' and 'values' parts");
        }

        $sql  = 'INSERT INTO ';
        $driver = $this;
        $tables = array_map(function ($x) use ($driver) {
            return $driver->quoteName($x);
        }, $query->tables);
        $sql .= implode(', ', $tables);
        $sql .= "\n";

        $sql .= '(';
        $sql .= implode(', ', array_map([get_called_class(), 'quoteName'], array_keys($query->values)));
        $sql .= ')';
        $sql .= "\n";

        $sql .= 'VALUES (';
        $sql .= implode(', ', $query->values);
        $sql .= ')';

        return $sql;
    }

    protected function makeQueryStringUpdate(Query $query)
    {
        if (empty($query->tables) || empty($query->values)) {
            throw new Exception("UPDATE statement must have both 'tables' and 'values' parts");
        }

        $sql  = 'UPDATE ';
        $driver = $this;
        $tables = array_map(function ($x) use ($driver) {
            return $driver->quoteName($x);
        }, $query->tables);
        $sql .= implode(', ', $tables);
        $sql .= "\n";

        $sets = [];
        foreach ($query->values as $key => $value) {
            $sets[] = static::quoteName($key) . '=' . $value;
        }

        $sql .= 'SET ' . implode(', ', $sets);
        $sql .= "\n";

        if (!empty($query->where)) {
            $sql .= 'WHERE ' . $query->where;
            $sql .= "\n";
        }

        if (!empty($query->order)) {
            $sql .= 'ORDER BY ' . implode(', ', $query->order);
            $sql .= "\n";
        }

        if (!empty($query->limit)) {
            $sql .= 'LIMIT ' . $query->limit;
            $sql .= "\n";
            if (!empty($query->offset)) {
                $sql .= 'OFFSET ' . $query->offset;
                $sql .= "\n";
            }
        }

        $sql = preg_replace('~\n$~', '', $sql);
        return $sql;
    }

    protected function makeQueryStringDelete(Query $query)
    {
        if (empty($query->tables)) {
            throw new Exception("DELETE statement must have 'tables' part");
        }

        $sql = '';

        if (!empty($query->with)) {
            $sql .= 'WITH ' . implode(', ', $query->with);
            $sql .= "\n";
        }

        $sql .= 'DELETE FROM ';
        $driver = $this;
        $tables = array_map(function ($x) use ($driver) {
            return $driver->quoteName($x);
        }, $query->tables);
        $sql .= implode(', ', $tables);
        $sql .= "\n";

        if (!empty($query->where)) {
            $sql .= 'WHERE ' . $query->where;
            $sql .= "\n";
        }

        if (!empty($query->order)) {
            $sql .= 'ORDER BY ' . implode(', ', $query->order);
            $sql .= "\n";
        }

        if (!empty($query->limit)) {
            $sql .= 'LIMIT ' . $query->limit;
            $sql .= "\n";
            if (!empty($query->offset)) {
                $sql .= 'OFFSET ' . $query->offset;
                $sql .= "\n";
            }
        }

        $sql = preg_replace('~\n$~', '', $sql);
        return $sql;
    }

}
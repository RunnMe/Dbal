<?php

namespace Running\Dbal\Drivers\Sqlite;

use Running\Dbal\DriverQueryBuilderInterface;
use Running\Dbal\Exception;
use Running\Dbal\Query;

/**
 * Class QueryBuilder
 * @package Running\Dbal\Drivers\Sqlite\Sqlite
 */
class QueryBuilder
    implements DriverQueryBuilderInterface
{

    // @todo: look for any function!
    protected $selectNoQouteTemplate = '~select|count|avg|group_concat|min|max|sum~i';

    public function quoteName($name)
    {
        $parts = explode('.', $name);
        $lastIndex = count($parts) - 1;
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
                !preg_match($this->selectNoQouteTemplate, $part)
            ) {
                $part = '`' . $part . '`';
            }
        }
        return implode('.', $parts);
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
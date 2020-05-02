<?php

namespace Runn\Dbal;

use Runn\Core\ArrayCastingInterface;
use Runn\Core\Std;

/**
 * Simple Query-Builder
 *
 * Class Query
 * @package Runn\Dbal
 *
 * @property string $string
 *
 * @property string $action
 *
 * @property array $columns
 * @property array $tables
 * @property array $with
 * @property array $joins
 *
 * @property string $where
 * @property array $group
 * @property string $having
 * @property array $order
 * @property int $offset
 * @property int $limit
 *
 * @property array $values
 * @property array $params Bound parameters {"name", "value", "type"}
 */
class Query extends Std implements ExecutableInterface
{

    /**
     * Query constructor.
     *
     * @param array|string $data
     */
    public function __construct($data = [])
    {
        if (is_string($data)) {
            $this->string = $data;
            return;
        }

        if ($data instanceof ArrayCastingInterface) {
            $data = $data->toArrayRecursive();
        }
        
        if (isset($data['action'])) {
            $this->action = $data['action'];
            unset($data['action']);
        }

        if (isset($data['select'])) {
            $this->select($data['select']);
            unset($data['select']);
        }
        if (isset($data['insert'])) {
            $this->insert($data['insert']);
            unset($data['insert']);
        }
        if (isset($data['update'])) {
            $this->update($data['update']);
            unset($data['update']);
        }

        if (isset($data['columns'])) {
            $this->columns($data['columns']);
            unset($data['columns']);
        }
        if (isset($data['tables'])) {
            $this->tables($data['tables']);
            unset($data['tables']);
        }
        if (isset($data['into'])) {
            $this->into($data['into']);
            unset($data['tables']);
        }
        if (isset($data['joins'])) {
            $this->joins($data['joins']);
            unset($data['joins']);
        }
        if (isset($data['group'])) {
            $this->group($data['group']);
            unset($data['group']);
        }
        if (isset($data['order'])) {
            $this->order($data['order']);
            unset($data['order']);
        }
        if (isset($data['values'])) {
            $this->set($data['values']);
            unset($data['values']);
        }
        if (isset($data['set'])) {
            $this->set($data['set']);
            unset($data['set']);
        }
        if (isset($data['params'])) {
            $this->params($data['params']);
            unset($data['params']);
        }
        $this->merge($data);
    }

    public function isString()
    {
        return !empty($this->string);
    }

    /**
     * Trims quotes from different names (columns, tables, etc)
     *
     * @param string $s
     * @return string
     */
    protected function trimName($s)
    {
        $trimmed = trim($s, " \"'`\t\n\r\0\x0B");
        if ( preg_match('~(\s|as\asc|desc)~i', $trimmed) ) {
            return $s;
        }
        $parts = explode('.', $trimmed);
        if (count($parts) > 1) {
            return implode('.', array_map([$this, 'trimName'], $parts));
        } else {
            return $trimmed;
        }
    }

    /**
     * @param array $names
     * @param bool $trim
     * @return array
     */
    protected function prepareNames($names = [], $trim = true)
    {
        if (1 == count($names)) {
            if (is_array($names[0])) {
                $names = $names[0];
            } else {
                $names = preg_split('~[\s]*\,[\s]*~', $names[0]);
            }
        }
        if ($trim) {
            $names = array_map([$this, 'trimName'], $names);
        }
        return $names;
    }

    /**
     * Set all columns and set action to select
     *
     * @param mixed $columns
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->columns(...func_get_args());
        $this->action = 'select';
        return $this;
    }

    /**
     * Set all tables
     *
     * @param mixed $tables
     * @return $this
     */
    public function from($tables = [])
    {
        $this->tables(...func_get_args());
        return $this;
    }

    /**
     * Set all query's WITH tables
     *
     * @param array $tables
     * @return $this
     */
    public function with($tables = [])
    {
        $tables = $this->prepareNames(func_get_args());
        $this->with = $tables;
        return $this;
    }

    /**
     * Set all tables and set action to insert
     *
     * @param mixed $tables
     * @return $this
     */
    public function into($tables = null)
    {
        if (null !== $tables) {
            $this->tables($tables);
        }
        $this->action = 'insert';
        return $this;
    }

    /**
     * Set all tables and set action to update
     *
     * @param mixed $tables
     * @return $this
     */
    public function update($tables = null)
    {
        if (null !== $tables) {
            $this->tables($tables);
        }
        $this->action = 'update';
        return $this;
    }

    /**
     * Set all tables and set action to delete
     *
     * @param mixed $tables
     * @return $this
     */
    public function delete($tables = null)
    {
        if (null !== $tables) {
            $this->tables($tables);
        }
        $this->action = 'delete';
        return $this;
    }

    /**
     * Add one or more table to query
     *
     * @param mixed $table
     * @return $this
     */
    public function table($table = [])
    {
        $tables = $this->prepareNames(func_get_args());
        $this->tables = array_merge($this->tables ?: [], $tables);
        return $this;
    }

    /**
     * Set all query's tables
     *
     * @param mixed $table
     * @return $this
     */
    public function tables($table = [])
    {
        $tables = $this->prepareNames(func_get_args());
        $this->tables = $tables;
        return $this;
    }

    /**
     * Add one column name to query
     *
     * @param mixed $column
     * @return $this
     */
    public function column($column = '*')
    {
        if ('*' == $column) {
            $this->columns = ['*'];
        } else {
            $columns = $this->prepareNames(func_get_args());
            $this->columns = array_merge(
                empty($this->columns) || ['*'] == $this->columns ? [] : $this->columns,
                array_values(array_diff($columns, ['*']))
            );
        }
        return $this;
    }

    /**
     * Set all query's columns
     *
     * @param mixed $columns
     * @return $this
     */
    public function columns($columns = '*')
    {
        if ('*' == $columns) {
            $this->columns = ['*'];
        } else {
            $columns = $this->prepareNames(func_get_args());
            $this->columns = array_values(array_diff($columns, ['*']));
        }
        return $this;
    }

    /**
     * Add one join statement to query
     *
     * @param string $table
     * @param string $on
     * @param string $type
     * @param string $alias
     * @return $this
     */
    public function join($table, $on, $type = 'full', $alias = '')
    {
        if (!isset($this->joins)) {
            $this->joins = [];
        }
        $join = [
            'table' => $this->trimName($table),
            'on' => $on,
            'type' => $type,
        ];
        if (!empty($alias)) {
            $join['alias'] = $this->trimName($alias);
        }
        $this->joins = array_merge($this->joins, [$join]);
        return $this;
    }

    /**
     * Set all query's joins
     *
     * @param array $joins
     * @return $this
     */
    public function joins($joins)
    {
        $this->joins = [];
        foreach ($joins as $join) {
            $this->join($join['table'], $join['on'], $join['type'] ?? 'full', $join['alias'] ?? '');
        }
        return $this;
    }

    /**
     * Sets WHERE condition
     *
     * @param string $where
     * @return $this
     */
    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * Sets group values
     *
     * @param string $group
     * @return $this
     */
    public function group($group)
    {
        $group = $this->prepareNames(func_get_args(), false);
        $this->group = $group;
        return $this;
    }

    /**
     * Sets HAVING condition
     *
     * @param string $having
     * @return $this
     */
    public function having($having)
    {
        $this->having = $having;
        return $this;
    }

    /**
     * Sets order directions
     *
     * @param string $order
     * @return $this
     */
    public function order($order)
    {
        $order = $this->prepareNames(func_get_args(), false);
        $this->order = $order;
        return $this;
    }

    /**
     * Sets offset
     *
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Sets limit
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Add one query's value for insert
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function value($key, $value)
    {
        $this->values = array_merge($this->values ?? [], [$this->trimName($key) => $value]);
        return $this;
    }

    /**
     * Add query's value (or all values) for update
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $values = array_combine(array_map([$this, 'trimName'], array_keys($key)), array_values($key));
            $this->values = $values;
            return $this;
        }
        $this->value($key, $value);
        return $this;
    }

    /**
     * Sets all query's values for insert
     *
     * @param array $values
     * @return $this
     */
    public function insert(array $values = [])
    {
        $values = array_combine(array_map([$this, 'trimName'], array_keys($values)), array_values($values));
        $this->values = $values;
        $this->action = 'insert';
        return $this;
    }

    /**
     * Binds a value to a parameter
     *
     * @param string $parameter
     * @param mixed $value
     * @return $this
     */
    public function param($parameter, $value, $type = Dbh::DEFAULT_PARAM_TYPE)
    {
        $this->params = array_merge($this->params ?? [], [['name' => $parameter, 'value' => $value, 'type' => $type]]);
        return $this;
    }

    /**
     * Sets all query's bind parameters
     *
     * @param iterable $params
     * @return $this
     */
    public function params(iterable $params = [])
    {
        $this->params = [];
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $this->params = array_merge($this->params ?? [], [$value]);
            } elseif (is_null($value)) { // TODO: Validation required for databases other than PostgreSQL
                $this->param($name, $value, Dbh::PARAM_NULL);
            } elseif (is_int($value)) { // TODO: Validation required for databases other than PostgreSQL
                $this->param($name, $value, Dbh::PARAM_INT);
            } elseif (is_bool($value)) { // TODO: Validation required for databases other than PostgreSQL
                $this->param($name, $value, Dbh::PARAM_BOOL);
            } else {
                $this->param($name, $value);
            }

        }
        return $this;
    }

    /**
     * Returns all query params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->__data['params'] ?? [];
    }

}

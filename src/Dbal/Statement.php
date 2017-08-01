<?php

namespace Runn\Dbal;

/**
 * Custom PDOStatement class with additional methods
 *
 * Class Statement
 * @package Runn\Dbal
 */
class Statement
    extends \PDOStatement
{

    /**
     * @param \Runn\Dbal\Query $query
     * @return \Runn\Dbal\Statement
     */
    public function bindQueryParams(Query $query)
    {
        foreach ($query->getParams() as $param) {
            if (isset($param['name'], $param['value'])) {
                $this->bindValue($param['name'], $param['value'], $param['type'] ?? Dbh::DEFAULT_PARAM_TYPE);
            }
        }
        return $this;
    }

    /**
     * Returns one (first) scalar column from the result
     * @return string
     */
    public function fetchScalar()
    {
        return $this->fetchColumn(0);
    }

    /**
     * Returns an array containing all of the result set rows as instances of the specified class, mapping the columns of each row to named properties in the class
     * @param string $class Class name
     * @param array $args Arguments of custom class constructor
     * @return array
     */
    public function fetchAllObjects($class, ...$args)
    {
        return $this->fetchAll(\PDO::FETCH_CLASS, $class, $args);
    }

}
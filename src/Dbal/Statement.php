<?php

namespace Runn\Dbal;

/**
 * Class Statement
 * @package Runn\Dbal
 */
class Statement
    extends \PDOStatement
{

    /**
     * @return string
     */
    public function fetchScalar()
    {
        return $this->fetchColumn(0);
    }

    /**
     * @param string $class
     * @return array
     */
    public function fetchAllObjects($class)
    {
        return $this->fetchAll(\PDO::FETCH_CLASS, $class);
    }

}
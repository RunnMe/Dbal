<?php

namespace Runn\Dbal;

use Runn\Core\TypedCollectionInterface;

/**
 * Custom PDOStatement class with additional helpful methods
 *
 * Class Statement
 * @package Runn\Dbal
 */
class Statement extends \PDOStatement
{

    /**
     * @param \Runn\Dbal\Query $query
     * @return \Runn\Dbal\Statement
     */
    public function bindQueryParams(Query $query)
    {
        foreach ($query->getParams() as $param) {
            if (isset($param['name'], $param['value'])) {
                if (isset($param['type'])) {
                    $type = $param['type'];
                } else {
                    if (is_null($param['value'])) {
                        $type = Dbh::PARAM_NULL;
                    } elseif (is_int($param['value'])) {
                        $type = Dbh::PARAM_INT;
                    } elseif (is_bool($param['value'])) {
                        $type = Dbh::PARAM_BOOL;
                    } else {
                        $type = Dbh::DEFAULT_PARAM_TYPE;
                    }
                }
                $this->bindValue($param['name'], $param['value'], $type);
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
     *
     * @param string $itemClass Item class name
     * @param array $args Arguments of custom item class constructor
     * @return array
     */
    public function fetchAllObjects($itemClass, ...$args)
    {
        return $this->fetchAll(\PDO::FETCH_CLASS, $itemClass, $args);
    }

    /**
     * Returns a typed collection containing all of the result set rows as instances of the specified class
     *
     * @param string $collectionClass Typed collection class name
     * @param string|null $itemClass Item class name
     * @param array $args Arguments of custom item class constructor
     * @return \Runn\Core\TypedCollectionInterface
     * @throws \Runn\Dbal\Exception
     */
    public function fetchAllObjectsCollection($collectionClass, $itemClass = null, ...$args)
    {
        if (!is_subclass_of($collectionClass, TypedCollectionInterface::class)) {
            throw new Exception('Invalid collection class: ' . $collectionClass);
        }
        if (null === $itemClass) {
            $itemClass = $collectionClass::getType();
        }
        return new $collectionClass($this->fetchAllObjects($itemClass, ...$args));
    }

}

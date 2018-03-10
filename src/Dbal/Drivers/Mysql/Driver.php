<?php

namespace Runn\Dbal\Drivers\Mysql;

use Runn\Dbal\DriverQueryBuilderInterface;

/**
 * DBAL MySQL driver
 *
 * Class Driver
 * @package Runn\Dbal\Drivers\Mysql
 */
class Driver
    extends \Runn\Dbal\Driver
{

    /**
     * @return \Runn\Dbal\DriverQueryBuilderInterface
     */
    public function getQueryBuilder(): DriverQueryBuilderInterface
    {
        $builder = new QueryBuilder;
        $builder->setDriver($this);
        return $builder;
    }

}

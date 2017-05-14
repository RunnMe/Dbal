<?php

namespace Runn\Dbal\Drivers\Mysql;

/**
 * MySQL DSN object
 * Class Dsn
 * @package Runn\Dbal\Drivers\Mysql
 */
class Dsn
    extends \Runn\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host'];
    /*protected */const OPTIONAL = ['port', 'dbname', 'charset'];

    public function getDriverDsnName(): string
    {
        return 'mysql';
    }

}
<?php

namespace Runn\Dbal\Drivers\Pgsql;

/**
 * PostgreSQL DSN object
 * Class Dsn
 * @package Runn\Dbal\Drivers\Pgsql
 */
class Dsn
    extends \Runn\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host'];
    /*protected */const OPTIONAL = ['port', 'dbname', 'user', 'password'];

    public function getDriverDsnName(): string
    {
        return 'pgsql';
    }

}
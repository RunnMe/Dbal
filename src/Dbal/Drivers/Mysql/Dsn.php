<?php

namespace Running\Dbal\Drivers\Mysql;

/**
 * MySQL DSN object
 * Class Dsn
 * @package Running\Dbal\Drivers\Mysql
 */
class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host'];
    /*protected */const OPTIONAL = ['port', 'dbname', 'charset'];

    public function getDriverDsnName(): string
    {
        return 'mysql';
    }

}
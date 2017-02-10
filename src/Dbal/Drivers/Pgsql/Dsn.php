<?php

namespace Running\Dbal\Drivers\Pgsql;

/**
 * PostgreSQL DSN object
 * Class Dsn
 * @package Running\Dbal\Drivers\Pgsql
 */
class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host'];
    /*protected */const OPTIONAL = ['port', 'dbname', 'user', 'password'];

}
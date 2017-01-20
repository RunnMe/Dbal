<?php

namespace Running\Dbal\Drivers\Pgsql;

class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['dbname'];
    /*protected */const OPTIONAL = ['host', 'port', 'user', 'password'];

}
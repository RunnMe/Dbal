<?php

namespace Running\Dbal\Drivers\Mysql;

class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = ['port', 'charset'];
}

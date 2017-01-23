<?php

namespace Running\Dbal\Drivers\Pgsql;

class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = ['port'];
}

<?php

namespace Runn\Dbal\Drivers\Sqlite;

/**
 * SQLite DSN object
 * Use 'file' => 'PATH_TO_FILE' or 'file'=>':memory:' for correct DSN
 *
 * Class Dsn
 * @package Runn\Dbal\Drivers\Sqlite
 */
class Dsn
    extends \Runn\Dbal\Dsn
{

    /*protected */const REQUIRED = ['file'];
    /*protected */const OPTIONAL = [];

    public function getDriverDsnName(): string
    {
        return 'sqlite';
    }

    public function __toString()
    {
        $dsn = $this->getDriverDsnName() . ':' . $this->config->file;
        return $dsn;
    }

}

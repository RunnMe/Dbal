<?php

namespace Running\Dbal\Drivers\Sqlite;

/**
 * SQLite DSN object
 * Use 'file' => 'PATH_TO_FILE' or 'file'=>':memory:' for correct DSN
 *
 * Class Dsn
 * @package Running\Dbal\Drivers\Sqlite
 */
class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['file'];
    /*protected */const OPTIONAL = [];

    public function getDriverDsnName(): string
    {
        return 'sqlite';
    }

    public function __toString()
    {
        $dsn = $this->config->driver . ':' . $this->config->file;
        return $dsn;
    }

}

<?php

namespace Running\Dbal\Drivers\Sqlite;

class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */const REQUIRED = ['file'];
    /*protected */const OPTIONAL = [];

    public function __toString()
    {
        return $this->config->driver . ':' . $this->config->file;
    }
}

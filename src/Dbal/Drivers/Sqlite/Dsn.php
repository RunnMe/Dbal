<?php

namespace Running\Dbal\Drivers\Sqlite;

class Dsn
    extends \Running\Dbal\Dsn
{

    /*protected */
    const REQUIRED = ['file'];
    /*protected */
    const OPTIONAL = [];

    public function __toString()
    {
        $parts = [];

        foreach ((array)static::REQUIRED as $required) {
            if ('file' == $required) {
                $parts[] = $this->config->$required;
            } else {
                $parts[] = $required . '=' . $this->config->$required;
            }
        }

        foreach ((array)static::OPTIONAL as $optional) {
            if (isset($this->config->$optional)) {
                $parts[] = $optional . '=' . $this->config->$optional;
            }
        }

        $dsn = $this->config->driver . ':' . implode(';', $parts);
        return $dsn;
    }
}

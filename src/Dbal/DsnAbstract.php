<?php

namespace Running\Dbal;

use Running\Core\Config;
use Running\Core\MultiException;

/**
 * Class Dsn
 * DSN maker
 * @package Running\Dbal
 */
abstract class DsnAbstract
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = [];

    /**
     * @var \Running\Core\Config
     */
    protected $config;

    /**
     * @param \Running\Core\Config $config
     * @throws \Running\Dbal\Exception
     */
    public function __construct(Config $config)
    {
        if (empty($config->driver)) {
            throw new Exception('Driver is empty in config');
        }
        $this->config = $config;
    }

    /**
     * @return string
     * @throws \Running\Core\MultiException
     */
    public function __toString()
    {
        $errors = new MultiException();

        foreach (static::REQUIRED as $required) {
            if (!isset($this->config->$required)) {
                $errors[] = new Exception('"' . $required . '" is not set in config');
            }
        }
        if (!$errors->isEmpty()) {
            throw $errors;
        }

        $parts = [];

        foreach (static::REQUIRED as $required) {
                $parts[] = $required . '=' . $this->config->$required;
        }

        foreach (static::OPTIONAL as $optional) {
            if (isset($this->config->$optional)) {
                $parts[] = $optional . '=' . $this->config->$optional;
            }
        }

        $dsn = $this->config->driver . ':' . implode(';', $parts);
        return $dsn;
    }

}
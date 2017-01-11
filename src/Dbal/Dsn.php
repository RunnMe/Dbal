<?php

namespace Running\Dbal;

use Running\Core\Config;
use Running\Core\MultiException;

/**
 * Class Dsn
 * DSN maker
 * @package Running\Dbal
 */
abstract class Dsn
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = [];

    /**
     * @var \Running\Core\Config
     */
    protected $config;

    /**
     * @param \Running\Core\Config $config
     * @throws \Running\Core\MultiException
     */
    public function __construct(Config $config)
    {
        $errors = new MultiException();

        if (empty($config->driver)) {
            $errors[] = new Exception('Driver is empty in config');
            throw $errors;
        }
        $this->config = $config;

        foreach ((array)static::REQUIRED as $required) {
            if (!isset($this->config->$required)) {
                $errors[] = new Exception('"' . $required . '" is not set in config');
            }
        }
        if (!$errors->isEmpty()) {
            throw $errors;
        }
    }

    /**
     * @param \Running\Core\Config $config
     * @return \Running\Dbal\Dsn
     * @throws \Running\Core\MultiException
     */
    public static function instance(Config $config)
    {
        if (empty($config->driver)) {
            throw (new MultiException())->add(new Exception('Driver is empty in config'));
        }
        try {
            $className = $config->class ?? __NAMESPACE__ . '\\Drivers\\' . ucfirst($config->driver) . '\\Dsn';
            return new $className($config);
        } catch (\Error $e) {
            throw (new MultiException())->add(new Exception('Driver is invalid', 0, $e));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parts = [];

        foreach ((array)static::REQUIRED as $required) {
                $parts[] = $required . '=' . $this->config->$required;
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
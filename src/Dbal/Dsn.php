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
    protected function __construct(Config $config)
    {
        $this->config = $config;

        $errors = new MultiException();

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
        try {

            if (!empty($config->class) && is_subclass_of($config->class, self::class)) {
                $className = $config->class;
            } elseif (!empty($config->driver) && is_subclass_of($config->driver, DriverInterface::class)) {
                $className = '\\' . implode('\\', array_slice(explode('\\', $config->driver), 0, -1)) . '\\Dsn';
            } else {
                throw (new MultiException())->add(new Exception('Can not suggest DSN class name'));
            }

            return new $className($config);

        } catch (\Error $e) {
            throw (new MultiException())->add(new Exception('Driver is invalid', 0, $e));
        }
    }

    abstract public function getDriverDsnName(): string;

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

        $dsn = $this->getDriverDsnName() . ':' . implode(';', $parts);
        return $dsn;
    }

}
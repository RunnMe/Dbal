<?php

namespace Runn\Dbal;

use Runn\Core\Config;
use Runn\Core\Exceptions;

/**
 * DSN constructor class
 *
 * Class Dsn
 * @package Runn\Dbal
 */
abstract class Dsn
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = [];

    /**
     * @var \Runn\Core\Config
     */
    protected $config;

    /**
     * @param \Runn\Core\Config $config
     * @throws \Runn\Core\Exceptions
     */
    protected function __construct(Config $config)
    {
        $this->config = $config;

        $errors = new Exceptions();

        foreach ((array)static::REQUIRED as $required) {
            if (!isset($this->config->$required)) {
                $errors[] = new Exception('"' . $required . '" is not set in config');
            }
        }

        if (!$errors->empty()) {
            throw $errors;
        }
    }

    /**
     * @param \Runn\Core\Config $config
     * @return \Runn\Dbal\Dsn
     * @throws \Runn\Core\Exceptions
     */
    public static function instance(Config $config)
    {
        if (!empty($config->class) && is_subclass_of($config->class, self::class)) {
            $className = $config->class;
        } elseif (!empty($config->driver) && is_subclass_of($config->driver, DriverInterface::class)) {
            $className = '\\' . implode('\\', array_slice(explode('\\', $config->driver), 0, -1)) . '\\Dsn';
            if (!class_exists($className) || !is_subclass_of($className, self::class)) {
                throw (new Exceptions())->add(new Exception('This driver has not DSN class'));
            }
        } else {
            throw (new Exceptions())->add(new Exception('Can not suggest DSN class name'));
        }

        return new $className($config);
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
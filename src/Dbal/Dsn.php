<?php

namespace Runn\Dbal;

use Runn\Core\Config;
use Runn\Core\ConfigAwareTrait;
use Runn\Core\Exceptions;
use Runn\Core\InstanceableByConfigInterface;

/**
 * DSN constructor class
 *
 * Class Dsn
 * @package Runn\Dbal
 */
abstract class Dsn
    implements InstanceableByConfigInterface
{

    /*protected */const REQUIRED = ['host', 'dbname'];
    /*protected */const OPTIONAL = [];

    use ConfigAwareTrait;

    /**
     * @param \Runn\Core\Config $config
     * @throws \Runn\Core\Exceptions
     */
    protected function __construct(Config $config)
    {
        $this->setConfig($config);

        $errors = new Exceptions();

        foreach ((array)static::REQUIRED as $required) {
            if (!isset($this->getConfig()->$required)) {
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
     * @throws \Runn\Dbal\Exception
     */
    public static function instance(Config $config = null)
    {
        if (null === $config) {
            throw new Exception('Empty DSN config');
        }

        if
        (!empty($config->class) && is_subclass_of($config->class, self::class)) {

            $className = $config->class;

        } elseif
        (!empty($config->driver) && is_subclass_of($config->driver, DriverInterface::class)) {

            $className = '\\' . implode('\\', array_slice(explode('\\', $config->driver), 0, -1)) . '\\Dsn';
            if (!class_exists($className) || !is_subclass_of($className, self::class)) {
                throw new Exception('This driver has not DSN class');
            }

        } elseif
        (get_called_class() != self::class) {

            $className = get_called_class();

        } else {
            throw new Exception('Can not suggest DSN class name');
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
                $parts[] = $required . '=' . $this->getConfig()->$required;
        }

        foreach ((array)static::OPTIONAL as $optional) {
            if (isset($this->getConfig()->$optional)) {
                $parts[] = $optional . '=' . $this->getConfig()->$optional;
            }
        }

        $dsn = $this->getDriverDsnName() . ':' . implode(';', $parts);
        return $dsn;
    }

}
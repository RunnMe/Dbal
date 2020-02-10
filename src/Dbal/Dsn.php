<?php

namespace Runn\Dbal;

use Runn\Core\Config;
use Runn\Core\ConfigAwareInterface;
use Runn\Core\ConfigAwareTrait;
use Runn\Core\Exceptions;
use Runn\Core\InstanceableByConfigInterface;

/**
 * DSN constructor class
 *
 * Class Dsn
 * @package Runn\Dbal
 */
abstract class Dsn implements ConfigAwareInterface, InstanceableByConfigInterface
{

    /** @var array Required DSN attributes */
    protected const REQUIRED = ['host', 'dbname'];

    /** @var array Optional DSN attributes */
    protected const OPTIONAL = [];

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
                $errors[] = new Exception('Attribute "' . $required . '" is not set in DSN config');
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

        if (!empty($config->class)) {
            if (is_subclass_of($config->class, self::class)) {
                $className = $config->class;
            } else {
                throw new Exception('Invalid DSN config "class" attribute: "' . $config->class . '" is not a DSN class name');
            }
        } elseif (!empty($config->driver)) {
            if (is_subclass_of($config->driver, DriverInterface::class)) {
                $className = $config->driver::getDsnClassName();
            } else {
                throw new Exception('Invalid DSN config "driver" attribute: "' . $config->driver . '" is not a Driver class name');
            }
        } elseif (is_subclass_of($class = get_called_class(), self::class)) {
            $className = $class;
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

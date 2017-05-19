<?php

namespace Runn\Dbal;

use Runn\Core\InstanceableInterface;

/**
 * Abstract DBAL driver class
 * Contains InstanceableInterface implementation
 *
 * Class Driver
 * @package Runn\Dbal
 */
abstract class Driver
    implements DriverInterface, InstanceableInterface
{

    /**
     * @param string $class
     * @return static
     * @throws \Runn\Dbal\Exception
     */
    public static function instance($class = null, ...$args)
    {
        if (null === $class) {
            $class = get_called_class();
        }
        if (!class_exists($class)) {
            throw new Exception('Driver class "' . $class . '" does not exists');
        }
        if (!is_subclass_of($class, DriverInterface::class)) {
            throw new Exception('Class "' . $class . '" is not a DBAL driver');
        }
        return new $class(...$args);
    }

}
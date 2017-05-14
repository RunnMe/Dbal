<?php

namespace Runn\Dbal;

/**
 * DBAL Drivers factory
 *
 * Class Drivers
 * @package Runn\Dbal
 */
class Drivers
{

    /**
     * @param string $class
     * @return DriverInterface
     * @throws Exception
     */
    public static function instance(string $class): DriverInterface
    {
        static $drivers = [];
        if (!isset($drivers[$class])) {
            if (!class_exists($class)) {
                throw new Exception('Driver class "' . $class . '" does not exists');
            }
            if (!is_subclass_of($class, DriverInterface::class)) {
                throw new Exception('Class "' . $class . '" is not a DBAL driver');
            }
            $drivers[$class] = new $class;
        }
        return $drivers[$class];
    }

}
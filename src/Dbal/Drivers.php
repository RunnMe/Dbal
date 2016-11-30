<?php

namespace Running\Dbal;

/**
 * DBAL Drivers factory
 *
 * Class Drivers
 * @package Running\Dbal
 */
class Drivers
{

    /**
     * @param string $driver
     * @return IDriver
     * @throws Exception
     */
    public static function instance($driver): IDriver
    {
        static $drivers = [];
        if (!isset($drivers[$driver])) {
            $driverClassName = __NAMESPACE__ . '\\Drivers\\' . ucfirst($driver);
            if (!class_exists($driverClassName)) {
                throw new Exception('Class ' . $driverClassName . ' does not exists');
            }
            $drivers[$driver] = new $driverClassName;
        }
        return $drivers[$driver];
    }

}
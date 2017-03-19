<?php

namespace Running\Dbal;

use Running\Core\Std;

/**
 * Abstract DB type class
 *
 * Class Column
 * @package Running\Dbal
 *
 * @property mixed $default
 */
abstract class Column
    extends Std
{

    /**
     * You need to realize this method for you own custom column types!
     *
     * @codeCoverageIgnore
     *
     * @param DriverInterface $driver
     * @return string
     */
    public function getColumnDdlByDriver(DriverInterface $driver)
    {
        return null;
    }

    /**
     * @param \Running\Dbal\DriverInterface $driver
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(DriverInterface $driver, $value)
    {
        return $value;
    }

    /**
     * @param \Running\Dbal\DriverInterface $driver
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(DriverInterface $driver, $value)
    {
        return $value;
    }

}
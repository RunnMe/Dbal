<?php

namespace Running\Dbal;

use Running\Core\Std;

/**
 * Abstract DB type class
 *
 * Class Column
 * @package Running\Dbal
 */
abstract class Column
    extends Std
{

    /**
     * You need to realize this method for you own custom column types!
     *
     * @param DriverInterface $driver
     * @return string
     */
    public function getColumnDdlByDriver(DriverInterface $driver)
    {
        return null;
    }

}
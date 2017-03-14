<?php

namespace Running\Dbal;

use Running\Core\Std;
use Running\Validation\Exceptions\EmptyValue;
use Running\Validation\Validators\ArrayValue;

/**
 * Abstract DB index schema class
 * Used for indexes creation
 *
 * Class Index
 * @package Running\Dbal
 *
 * @property array $columns
 *
 * @property string $table
 * @property string $name
 */
abstract class Index
    extends Std
{
    protected static $required = ['columns'];

    protected function needCasting($key, $value): bool
    {
        if ('columns' == $key) {
            return false;
        } else {
            return parent::needCasting($key, $value);
        }
    }

    protected function validateColumns($value)
    {
        (new ArrayValue())->validate($value);
        if (empty($value)) {
            throw new EmptyValue($value);
        }
        return true;
    }

    /**
     * You need to realize this method for you own custom index types!
     *
     * @param DriverInterface $driver
     * @return string
     */
    public function getIndexDdlByDriver(DriverInterface $driver)
    {
        return null;
    }

}

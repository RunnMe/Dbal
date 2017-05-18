<?php

namespace Runn\Dbal;

use Runn\Core\Std;
use Runn\Validation\Exceptions\EmptyValue;
use Runn\Validation\Validators\ArrayValidator;

/**
 * Abstract DB index schema class
 * Used for indexes creation
 *
 * Class Index
 * @package Runn\Dbal
 *
 * @property array $columns
 *
 * @property string $table
 * @property string $name
 */
abstract class Index
    extends Std
{

    protected function needCasting($key, $value): bool
    {
        return false;
    }

    protected function validateColumns($value)
    {
        (new ArrayValidator())->validate($value);
        if (empty($value) || 0 == count($value)) {
            throw new EmptyValue($value);
        }
        return true;
    }

    /**
     * You need to realize this method for you own custom index types!
     *
     * @param DriverInterface $driver
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getIndexDdlByDriver(DriverInterface $driver)
    {
        return null;
    }

}
<?php

namespace Runn\Dbal;

use Runn\Core\Std;
use Runn\Validation\Exceptions\EmptyValue;
use Runn\Validation\Validators\ArrayValue;

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
    protected static $required = ['columns', 'name'];

    /**
     * Checks if all required properties are set
     * @return bool
     * @throws \Runn\Dbal\Exception
     */
    protected function checkRequired()
    {
        $one = false;
        foreach ($this->getRequiredProperties() as $required) {
            if (isset($this->$required)) {
                $one = $one || true;
            }
        }
        if (!$one) {
            throw new Exception('You need at least one of [' . implode(', ', $this->getRequiredProperties()) . '] to be set');
        }
        return true;
    }

    protected function needCasting($key, $value): bool
    {
        return false;
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

<?php

namespace Runn\Dbal;

use Runn\Core\ObjectAsArrayInterface;
use Runn\Core\TypedCollection;

class Columns
    extends TypedCollection
{

    public static function getType()
    {
        return Column::class;
    }

    /**
     * Does value need cast to this (or another) class?
     * @param mixed $value
     * @return bool
     */
    protected function needCasting($key, $value): bool
    {
        if ($value instanceof ObjectAsArrayInterface) {
            return true;
        }
        return parent::needCasting($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function innerCast($key, $value)
    {
        if (is_array($value) || $value instanceof ObjectAsArrayInterface) {
            if (isset($value['class']) && is_string($value['class']) && is_subclass_of($value['class'], self::getType())) {
                $class = $value['class'];
                unset($value['class']);
                $value = new $class($value);
            }
        }
        return $value;
    }

}
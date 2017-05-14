<?php

namespace Runn\Dbal;

use Runn\Core\TypedCollection;

class Columns
    extends TypedCollection
{

    public static function getType()
    {
        return Column::class;
    }

    /**
     * @param iterable $data
     * @return $this
     */
    public function fromArray(/* iterable */ $data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                if (!empty($class = $value['class']) && is_subclass_of($class, self::getType())) {
                    unset($value['class']);
                    $value = new $class($value);
                }
            }
        }
        return parent::fromArray($data);
    }

}
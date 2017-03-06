<?php

namespace Running\Dbal;

use Running\Core\TypedCollection;

class Columns
    extends TypedCollection
{

    public static function getType()
    {
        return Column::class;
    }

}
<?php

namespace Runn\Dbal;

use Runn\Core\TypedCollection;

/**
 * Queries collection
 * Can be executed (every query in collection)
 * @todo: transaction(=false)
 *
 * Class Queries
 * @package Dbal
 */
class Queries extends TypedCollection implements ExecutableInterface
{

    public static function getType()
    {
        return Query::class;
    }

}

<?php

namespace Runn\Dbal;

use Runn\Core\TypedCollectionInterface;
use Runn\Core\TypedCollectionTrait;

/**
 * Queries collection
 * Can be executed
 * @todo: transaction(true)
 *
 * Class Queries
 * @package Dbal
 */
class Queries
    implements TypedCollectionInterface, ExecutableInterface
{

    use TypedCollectionTrait;

    public static function getType()
    {
        return Query::class;
    }

}
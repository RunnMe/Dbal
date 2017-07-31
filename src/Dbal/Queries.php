<?php

namespace Dbal;

use Runn\Core\TypedCollectionInterface;
use Runn\Core\TypedCollectionTrait;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Query;

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
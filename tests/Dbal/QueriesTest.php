<?php

namespace Runn\tests\Dbal\Queries;

use Dbal\Queries;
use Runn\Core\CollectionInterface;
use Runn\Core\TypedCollectionInterface;
use Runn\Dbal\Query;

class QueriesTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $queries = new Queries();
        $this->assertInstanceOf(Queries::class, $queries);
        $this->assertInstanceOf(TypedCollectionInterface::class, $queries);
        $this->assertInstanceOf(CollectionInterface::class, $queries);
        $this->assertSame(Query::class, $queries->getType());
    }

}
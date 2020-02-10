<?php

namespace Runn\tests\Dbal\Queries;

use PHPUnit\Framework\TestCase;
use Runn\Core\CollectionInterface;
use Runn\Core\TypedCollection;
use Runn\Core\TypedCollectionInterface;
use Runn\Dbal\ExecutableInterface;
use Runn\Dbal\Queries;
use Runn\Dbal\Query;

class QueriesTest extends TestCase
{

    public function testInstance()
    {
        $queries = new Queries();
        $this->assertInstanceOf(Queries::class, $queries);
        $this->assertInstanceOf(TypedCollectionInterface::class, $queries);
        $this->assertInstanceOf(CollectionInterface::class, $queries);
        $this->assertInstanceOf(TypedCollection::class, $queries);
        $this->assertInstanceOf(ExecutableInterface::class, $queries);
        $this->assertSame(Query::class, $queries->getType());
    }

}

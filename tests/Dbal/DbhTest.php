<?php

namespace Runn\tests\Dbal\Dbh;

use Runn\Dbal\Dbh;

class DbhTest extends \PHPUnit_Framework_TestCase
{

    public function testInstance()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertInstanceOf(\PDO::class, $dbh);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBeginTransaction()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $dbh->beginTransaction();
        $this->fail();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCommit()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $dbh->commit();
        $this->fail();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testRollBack()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $dbh->rollBack();
        $this->fail();
    }

}
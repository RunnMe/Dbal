<?php

namespace Runn\tests\Dbal\Dbh;

use Runn\Core\Config;
use Runn\Dbal\Dbh;
use Runn\Dbal\Exception;
use Runn\Dbal\Statement;

class testStatement extends Statement {}

class DbhTest extends \PHPUnit_Framework_TestCase
{

    public function testInheritance()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertInstanceOf(\PDO::class, $dbh);
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Empty DBH config
     */
    public function testInstanceByConfigNull()
    {
        $dbh = Dbh::instance();
    }

    /**
     * @expectedException \Runn\Dbal\Exception
     * @expectedExceptionMessage Can not suggest DSN class name
     */
    public function testInstanceByConfigEmptyDsn()
    {
        $dbh = Dbh::instance(new Config(['withoutDsn' => true]));
    }

    public function testInstanceByConfig()
    {
        $dbh = Dbh::instance(new Config([
            'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class,
            'file' => ':memory:'
        ]));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_EXCEPTION, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([Statement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));

        $dbh = Dbh::instance(new Config([
            'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class,
            'file' => ':memory:',
            'errmode' => Dbh::ERRMODE_SILENT,
            'statement' => testStatement::class
        ]));

        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertEquals(Dbh::ERRMODE_SILENT, $dbh->getAttribute(Dbh::ATTR_ERRMODE));
        $this->assertEquals([testStatement::class], $dbh->getAttribute(Dbh::ATTR_STATEMENT_CLASS));

        $dbh = Dbh::instance(new Config([
            'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class,
            'file' => ':memory:',
            'options' => [Dbh::ATTR_CASE => Dbh::CASE_UPPER]
        ]));
        $this->assertEquals(Dbh::CASE_UPPER, $dbh->getAttribute(Dbh::ATTR_CASE));
    }

    public function testInstanceDbhException()
    {
        try {
            $dbh = Dbh::instance(new Config(['driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 'file' => '/Invalid/File/Name']));
            $this->fail();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious());
            return;
        }
        $this->fail();
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
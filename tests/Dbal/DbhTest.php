<?php

namespace Runn\tests\Dbal\Dbh;

use PHPUnit\Framework\TestCase;
use Runn\Core\Config;
use Runn\Dbal\Dbh;
use Runn\Dbal\Exception;
use Runn\Dbal\Statement;

class testStatement extends Statement {}

class DbhTest extends TestCase
{

    public function testInheritance()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();
        $this->assertInstanceOf(Dbh::class, $dbh);
        $this->assertInstanceOf(\PDO::class, $dbh);
    }

    public function testInstanceWithoutConfig()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty DSN config');
        $dbh = Dbh::instance();
    }

    public function testInstanceByConfigWithEmptyDsn()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can not suggest DSN class name');
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

    public function testBeginTransaction()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();

        $this->expectException(\BadMethodCallException::class);
        $dbh->beginTransaction();
    }

    public function testCommit()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();

        $this->expectException(\BadMethodCallException::class);
        $dbh->commit();
    }

    public function testRollBack()
    {
        $reflector = new \ReflectionClass(Dbh::class);
        $dbh = $reflector->newInstanceWithoutConstructor();

        $this->expectException(\BadMethodCallException::class);
        $dbh->rollBack();
    }

}

<?php

namespace Running\tests\Dbal\Dsn;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\Dsn;

class testDsn extends Dsn {
    const REQUIRED = ['foo', 'bar'];
    const OPTIONAL = ['baz'];
}

class DsnATest extends \PHPUnit_Framework_TestCase
{

    public function testConstructValid()
    {
        $config = new Config(['driver' => 'sqlite', 'foo' => 'test', 'bar' => 'bla']);
        $dsn = new testDsn($config);

        $this->assertInstanceOf(Dsn::class, $dsn);

        $reflector = new \ReflectionObject($dsn);
        $property = $reflector->getProperty('config');
        $property->setAccessible(true);
        $this->assertEquals(
            $property->getValue($dsn),
            $config
        );
    }

    public function testConstructWithNoDriver()
    {
        try {
            $dsn = new testDsn(
                new Config(['nodriver' => 'invalid'])
            );
        } catch (MultiException $errors) {
            $this->assertCount(1, $errors);
            $this->assertEquals('Driver is empty in config', $errors[0]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testConstructWithNoRequired()
    {
        try {
            $dsn = new testDsn(
                new Config(['driver' => 'sqlite'])
            );
        } catch (MultiException $errors) {
            $this->assertCount(2, $errors);
            $this->assertEquals('"foo" is not set in config', $errors[0]->getMessage());
            $this->assertEquals('"bar" is not set in config', $errors[1]->getMessage());
            return;
        }
        $this->fail();
    }

    public function testToString()
    {
        $config = new Config(['driver' => 'sqlite', 'foo' => 'test', 'bar' => 'bla']);
        $dsn = new testDsn($config);
        $this->assertEquals('sqlite:foo=test;bar=bla', (string)$dsn);

        $config = new Config(['driver' => 'sqlite', 'foo' => 'test', 'bar' => 'bla', 'baz' => 42]);
        $dsn = new testDsn($config);
        $this->assertEquals('sqlite:foo=test;bar=bla;baz=42', (string)$dsn);
    }

    public function testInstance()
    {
        $dsn = Dsn::instance(new Config(['driver' => 'sqlite']));
        $this->assertInstanceOf(Dsn::class, $dsn);
        $this->assertInstanceOf(\Running\Dbal\Drivers\Sqlite\Dsn::class, $dsn);
    }

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Driver is empty in config
     */
    public function testInstanceInvalid1()
    {
        $dsn = Dsn::instance(new Config(['nodriver' => 'invalid']));
    }

    /**
     * @expectedException \Running\Dbal\Exception
     * @expectedExceptionMessage Driver is invalid
     */
    public function testInstanceInvalid2()
    {
        $dsn = Dsn::instance(new Config(['driver' => 'invalid']));
    }

}
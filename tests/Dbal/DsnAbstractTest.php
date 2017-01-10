<?php

namespace Running\tests\Dbal\DsnAbstract;

use Running\Core\Config;
use Running\Core\MultiException;
use Running\Dbal\DsnAbstract;

class testDsn extends DsnAbstract {
    const REQUIRED = ['foo'];
}

class DsnAbstractTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructValid()
    {
        $config = new Config(['driver' => 'sqlite', 'foo' => 'bar']);
        $dsn = new testDsn($config);

        $this->assertInstanceOf(DsnAbstract::class, $dsn);

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

    /*
    public function testRequiredIsNotSet()
    {
        try {
            $dsn = new testDsn(new Config(['driver' => 'some']));
            (string)$dsn;
        } catch (MultiException $errors) {
            $this->assertCount(5, $errors);
            return;
        }
        $this->fail();
    }
    */

}
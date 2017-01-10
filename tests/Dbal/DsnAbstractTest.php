<?php

namespace Running\tests\Dbal\DsnAbstract;

use Running\Core\Config;
use Running\Dbal\DsnAbstract;

class testDsn extends DsnAbstract {}

class DsnAbstractTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructValid()
    {
        $config = new Config(['driver' => 'sqlite']);
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

    /**
     * @expectedException \Running\Dbal\Exception
     */
    public function testConstructInvalid()
    {
        $dsn = new testDsn(
            new Config(['nodriver' => 'invalid'])
        );
        $this->fail();
    }

}
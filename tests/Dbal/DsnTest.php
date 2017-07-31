<?php

namespace Runn\Dbal\Drivers\Test {

    class Dsn extends \Runn\Dbal\Dsn {
        const REQUIRED = ['foo', 'bar'];
        const OPTIONAL = ['baz'];
        public function getDriverDsnName(): string {
            return 'test';
        }
    }

}

namespace Runn\tests\Dbal\Dsn {

    use Runn\Core\Config;
    use Runn\Core\Exceptions;
    use Runn\Dbal\Dsn;
    use Runn\Dbal\Exception;

    class DsnTest extends \PHPUnit_Framework_TestCase
    {

        public function testInstanceValidDsnClass()
        {
            $config = new Config(['foo' => 'test', 'bar' => 'bla']);
            $dsn = \Runn\Dbal\Drivers\Test\Dsn::instance($config);

            $this->assertInstanceOf(\Runn\Dbal\Drivers\Test\Dsn::class, $dsn);
            $this->assertInstanceOf(Dsn::class, $dsn);

            $reflector = new \ReflectionObject($dsn);
            $property = $reflector->getProperty('config');
            $property->setAccessible(true);
            $this->assertEquals(
                $property->getValue($dsn),
                $config
            );
        }

        public function testInstanceValidDsnClassInConfig()
        {
            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla']);
            $dsn = Dsn::instance($config);

            $this->assertInstanceOf(\Runn\Dbal\Drivers\Test\Dsn::class, $dsn);
            $this->assertInstanceOf(Dsn::class, $dsn);

            $reflector = new \ReflectionObject($dsn);
            $property = $reflector->getProperty('config');
            $property->setAccessible(true);
            $this->assertEquals(
                $property->getValue($dsn),
                $config
            );
        }

        public function testInstanceValidDriver()
        {
            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla']);
            $dsn = Dsn::instance($config);

            $this->assertInstanceOf(\Runn\Dbal\Drivers\Test\Dsn::class, $dsn);
            $this->assertInstanceOf(Dsn::class, $dsn);

            $reflector = new \ReflectionObject($dsn);
            $property = $reflector->getProperty('config');
            $property->setAccessible(true);
            $this->assertEquals(
                $property->getValue($dsn),
                $config
            );
        }

        /**
         * @expectedException \Runn\Dbal\Exception
         * @expectedExceptionMessage Empty DSN config
         */
        public function testInstanceWithNoDriver()
        {
            $dsn = Dsn::instance();
        }

        /**
         * @expectedException \Runn\Dbal\Exception
         * @expectedExceptionMessage Can not suggest DSN class name
         */
        public function testInstanceWithInvalidDriver()
        {
            $dsn = Dsn::instance(
                new Config(['driver' => 'invalid'])
            );
        }

        /**
         * @expectedException \Runn\Dbal\Exception
         * @expectedExceptionMessage This driver has not DSN class
         */
        public function testInstanceWithValidDriverWithoutDsn()
        {
            require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';
            $dsn = Dsn::instance(
                new Config(['driver' => \Runn\tests\Dbal\Drivers\WithoutDsn\Driver::class])
            );
        }

        public function testInstanceWithNoRequired()
        {
            try {
                $dsn = Dsn::instance(
                    new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class])
                );
            } catch (Exceptions $errors) {
                $this->assertCount(2, $errors);
                $this->assertInstanceOf(Exception::class, $errors[0]);
                $this->assertEquals('"foo" is not set in config', $errors[0]->getMessage());
                $this->assertInstanceOf(Exception::class, $errors[1]);
                $this->assertEquals('"bar" is not set in config', $errors[1]->getMessage());
                return;
            }
            $this->fail();
        }

        public function testToString()
        {
            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla']);
            $dsn = Dsn::instance($config);
            $this->assertEquals('test:foo=test;bar=bla', (string)$dsn);

            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla', 'baz' => 42]);
            $dsn = Dsn::instance($config);
            $this->assertEquals('test:foo=test;bar=bla;baz=42', (string)$dsn);
        }

    }

}
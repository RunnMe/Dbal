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

        public function testInstanceWithNoDriver()
        {
            try {
                $dsn = Dsn::instance(
                    new Config(['nodriver' => 'invalid'])
                );
            } catch (Exceptions $errors) {
                $this->assertCount(1, $errors);
                $this->assertInstanceOf(Exception::class, $errors[0]);
                $this->assertEquals('Can not suggest DSN class name',  $errors[0]->getMessage());
                return;
            }
            $this->fail();
        }

        public function testInstanceWithInvalidDriver()
        {
            try {
                $dsn = Dsn::instance(
                    new Config(['driver' => 'invalid'])
                );
            } catch (Exceptions $errors) {
                $this->assertCount(1, $errors);
                $this->assertInstanceOf(Exception::class, $errors[0]);
                $this->assertEquals('Can not suggest DSN class name', $errors[0]->getMessage());
                return;
            }
            $this->fail();
        }

        public function testInstanceWithValidDriverWithoutDsn()
        {
            require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';
            try {
                $dsn = Dsn::instance(
                    new Config(['driver' => \Runn\tests\Dbal\Drivers\WithoutDsn\Driver::class])
                );
            } catch (Exceptions $errors) {
                $this->assertCount(1, $errors);
                $this->assertInstanceOf(Exception::class, $errors[0]);
                $this->assertEquals('This driver has not DSN class', $errors[0]->getMessage());
                return;
            }
            $this->fail();
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
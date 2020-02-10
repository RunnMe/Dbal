<?php

namespace Runn\Dbal\Drivers\Test {

    use Runn\Dbal\DriverQueryBuilderInterface;

    /**
     * Test driver class
     *
     * Class Driver
     * @package Runn\Dbal\Drivers\Test
     */
    class Driver extends \Runn\Dbal\Driver {
        public static function getDsnClassName(): string {
            return Dsn::class;
        }
        public function getQueryBuilder(): DriverQueryBuilderInterface {
        }
    }

    /**
     * Test DSN class
     *
     * Class Dsn
     * @package Runn\Dbal\Drivers\Test
     */
    class Dsn extends \Runn\Dbal\Dsn {
        protected const REQUIRED = ['foo', 'bar'];
        protected const OPTIONAL = ['baz'];
        public function getDriverDsnName(): string {
            return 'test';
        }
    }

}

namespace Runn\tests\Dbal\Dsn {

    use PHPUnit\Framework\TestCase;
    use Runn\Core\Config;
    use Runn\Core\Exceptions;
    use Runn\Dbal\Dsn;
    use Runn\Dbal\Exception;

    class DsnTest extends TestCase
    {

        public function testInstanceWithNoRequired()
        {
            try {
                $dsn = Dsn::instance(
                    new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class])
                );
            } catch (Exceptions $errors) {
                $this->assertCount(2, $errors);

                $this->assertInstanceOf(Exception::class, $errors[0]);
                $this->assertEquals('Attribute "foo" is not set in DSN config', $errors[0]->getMessage());

                $this->assertInstanceOf(Exception::class, $errors[1]);
                $this->assertEquals('Attribute "bar" is not set in DSN config', $errors[1]->getMessage());
                return;
            }
            $this->fail();
        }

        public function testInstanceWithoutConfig()
        {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Empty DSN config');
            $dsn = Dsn::instance();
        }

        public function testInstanceWithValidDsnClassInConfig()
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

        public function testInstanceWithInvalidDsnClassInConfig()
        {
            $config = new Config(['class' => \stdClass::class]);

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Invalid DSN config "class" attribute: "stdClass" is not a DSN class name');
            $dsn = Dsn::instance($config);
        }

        public function testInstanceWithValidDriverClassInConfig()
        {
            $config = new Config(['driver' => \Runn\Dbal\Drivers\Test\Driver::class, 'foo' => 'test', 'bar' => 'bla']);
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

        public function testInstanceWithInvalidDriverClassInConfig()
        {
            $config = new Config(['driver' => \stdClass::class]);

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Invalid DSN config "driver" attribute: "stdClass" is not a Driver class name');
            $dsn = Dsn::instance($config);
        }

        public function testInstanceWithValidDriverWithoutDsn()
        {
            require_once __DIR__ . '/Drivers/WithoutDsn/Driver.php';

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('This driver has not DSN class');
            $dsn = Dsn::instance(
                new Config(['driver' => \Runn\tests\Dbal\Drivers\WithoutDsn\Driver::class])
            );
        }

        public function testInstanceWithValidDsnClass()
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

        public function testToString()
        {
            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla']);
            $dsn = Dsn::instance($config);
            $this->assertEquals('test:foo=test;bar=bla', (string)$dsn);

            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla', 'baz' => 42]);
            $dsn = Dsn::instance($config);
            $this->assertEquals('test:foo=test;bar=bla;baz=42', (string)$dsn);

            $config = new Config(['class' => \Runn\Dbal\Drivers\Test\Dsn::class, 'foo' => 'test', 'bar' => 'bla', 'baz' => 42, 'one' => 1, 'two' => 2]);
            $dsn = Dsn::instance($config);
            $this->assertEquals('test:foo=test;bar=bla;baz=42', (string)$dsn);
        }

    }

}

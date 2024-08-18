<?php

declare(strict_types=1);

namespace TestCase;

use Cake\Core\Container;
use Cake\TestSuite\TestCase;
use Passchn\SimpleDI\Module\DI\Exception\ServiceNotCreated;
use Passchn\SimpleDI\Module\ServiceLocator\Exception\ContainerNotSet;
use Passchn\SimpleDI\Module\ServiceLocator\ServiceLocator;
use Passchn\SimpleDI\Tests\Stub\AnotherTestClass;
use Passchn\SimpleDI\Tests\Stub\TestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use RuntimeException;

#[CoversClass(ServiceLocator::class)]
#[UsesClass(Container::class)]
class ServiceLocatorTest extends TestCase
{
    public function testWithContainerNotSet(): void
    {
        self::expectException(ContainerNotSet::class);
        ServiceLocator::get('someKey');
    }

    public function testGet(): void
    {
        $container = $this->createContainer();
        ServiceLocator::setContainer($container);

        $container->add('someKey', 'someValue');
        $someValue = ServiceLocator::get('someKey');

        self::assertEquals('someValue', $someValue);
    }

    public function testResolveInstance(): void
    {
        $container = $this->createContainer();
        ServiceLocator::setContainer($container);

        $container->add(TestClass::class, fn () => new AnotherTestClass('I am a wrong instance'));

        self::expectException(RuntimeException::class);
        ServiceLocator::resolveInstance(TestClass::class);
    }

    public function testHas(): void
    {
        $container = $this->createContainer();
        ServiceLocator::setContainer($container);

        $container->add('someKey', 'someValue');

        self::assertTrue(ServiceLocator::has('someKey'));
        self::assertFalse(ServiceLocator::has('anotherKey'));
    }

    public function testGetNew(): void
    {
        $container = $this->createContainer();
        $container->defaultToShared();
        ServiceLocator::setContainer($container);

        $container->add(
            TestClass::class,
            fn() => new TestClass('hello'),
        );

        $stdClass1 = ServiceLocator::get(TestClass::class);
        $stdClass2 = ServiceLocator::get(TestClass::class);

        self::assertEquals($stdClass1, $stdClass2);
        self::assertSame($stdClass1, $stdClass2);

        $stdClass3 = ServiceLocator::getNew(TestClass::class);

        self::assertNotSame($stdClass1, $stdClass3);
        self::assertNotSame($stdClass2, $stdClass3);
        self::assertEquals($stdClass1, $stdClass3);
        self::assertEquals($stdClass2, $stdClass3);
        self::assertSame($stdClass1->someProperty, $stdClass3->someProperty);
    }

    private function createContainer(): Container
    {
        return new Container();
    }
}
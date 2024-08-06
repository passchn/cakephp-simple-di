<?php

declare(strict_types=1);

namespace TestCase;

use Cake\Core\Container;
use Cake\TestSuite\TestCase;
use Passchn\SimpleDI\Module\DI\DIManager;
use Passchn\SimpleDI\Module\DI\Exception\ServiceNotCreated;
use Passchn\SimpleDI\Tests\Stub\AnotherTestClass;
use Passchn\SimpleDI\Tests\Stub\TestClass;
use Passchn\SimpleDI\Tests\Stub\AbstractTestFactory;
use Passchn\SimpleDI\Tests\Stub\TestModule;
use Passchn\SimpleDI\Tests\Stub\TestPlugin;
use Passchn\SimpleDI\Tests\Stub\YetAnotherTestClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(DIManager::class)]
#[UsesClass(Container::class)]
class DIManagerTest extends TestCase
{
    public function testAddService(): void
    {
        $container = $this->createContainer();
        DIManager::create($container)->addService(
            TestClass::class,
            fn() => new TestClass('hello'),
        );

        $instance = $container->get(TestClass::class);

        self::assertInstanceOf(TestClass::class, $instance);
        assert($instance instanceof TestClass);
        self::assertSame('hello', $instance->someProperty);
    }

    public function testAddServices(): void
    {
        $container = $this->createContainer();
        DIManager::create($container)->addServices([
            TestClass::class => fn() => new TestClass('hello'),
        ]);

        $instance = $container->get(TestClass::class);

        self::assertInstanceOf(TestClass::class, $instance);
        assert($instance instanceof TestClass);
        self::assertSame('hello', $instance->someProperty);
    }

    public function testAddModules(): void
    {
        $container = $this->createContainer();
        DIManager::create($container)->addModules([
            TestModule::class,
        ]);

        $instance = $container->get(TestClass::class);

        self::assertInstanceOf(TestClass::class, $instance);
        assert($instance instanceof TestClass);
        self::assertSame('created by module', $instance->someProperty);

        $anotherInstance = $container->get(AnotherTestClass::class);

        self::assertInstanceOf(AnotherTestClass::class, $anotherInstance);
        assert($anotherInstance instanceof AnotherTestClass);
        self::assertSame('created by factory', $anotherInstance->someProperty);
    }

    public function testAddPlugins(): void
    {
        $container = $this->createContainer();
        DIManager::create($container)->addPlugins([
            TestPlugin::class,
        ]);

        $instance = $container->get(TestClass::class);

        self::assertInstanceOf(TestClass::class, $instance);
        assert($instance instanceof TestClass);
        self::assertSame('created by module', $instance->someProperty);

        $anotherInstance = $container->get(AnotherTestClass::class);

        self::assertInstanceOf(AnotherTestClass::class, $anotherInstance);
        assert($anotherInstance instanceof AnotherTestClass);
        self::assertSame('created by factory', $anotherInstance->someProperty);
    }

    public function testAbstractFactory(): void
    {
        $container = $this->createContainer();
        DIManager::create($container)->addServices([
            AnotherTestClass::class => AbstractTestFactory::class,
            YetAnotherTestClass::class => AbstractTestFactory::class,
            TestClass::class => AbstractTestFactory::class, // not handled
        ]);

        $instance = $container->get(AnotherTestClass::class);
        self::assertInstanceOf(AnotherTestClass::class, $instance);

        $instance = $container->get(YetAnotherTestClass::class);
        self::assertInstanceOf(YetAnotherTestClass::class, $instance);

        self::expectException(ServiceNotCreated::class);
        $instance = $container->get(TestClass::class);
    }

    private function createContainer(): Container
    {
        return new Container();
    }
}
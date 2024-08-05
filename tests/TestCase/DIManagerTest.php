<?php

declare(strict_types=1);

namespace TestCase;

use Cake\Core\Container;
use Cake\TestSuite\TestCase;
use Passchn\SimpleDI\Module\DI\DIManager;
use Passchn\SimpleDI\Tests\Stub\AnotherTestClass;
use Passchn\SimpleDI\Tests\Stub\TestClass;
use Passchn\SimpleDI\Tests\Stub\TestModule;
use Passchn\SimpleDI\Tests\Stub\TestPlugin;
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

    private function createContainer(): Container
    {
        return new Container();
    }
}
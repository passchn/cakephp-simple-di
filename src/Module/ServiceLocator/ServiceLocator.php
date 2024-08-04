<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\ServiceLocator;

use Cake\Core\ContainerInterface;

/**
 * The ServiceLocator pattern is an Anti-Pattern.
 *
 * This class should therefore only be used where proper Dependency Injection is not possible.
 *
 * Register the container in your Application::services function.
 * The container will not be available before Application::services was called.
 *
 * @link https://de.wikipedia.org/wiki/Service-Locator
 */
class ServiceLocator
{
    protected static ?ContainerInterface $instance = null;

    public static function getContainer(): ContainerInterface
    {
        if (static::$instance instanceof ContainerInterface) {
            return static::$instance;
        }

        throw new \LogicException('container instance was not set');
    }

    public static function setContainer(ContainerInterface $container): void
    {
        static::$instance = $container;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public static function get(string $id)
    {
        return static::getContainer()->get($id);
    }

    public static function has(string $id): bool
    {
        return static::getContainer()->has($id);
    }
}

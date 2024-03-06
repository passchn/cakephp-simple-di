<?php

declare(strict_types=1);

namespace SimpleDI\Module\ServiceLocator;

use Cake\Core\ContainerInterface as CakeContainerInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

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
    protected static ?PsrContainerInterface $instance = null;

    public static function getContainer(): PsrContainerInterface
    {
        if (static::$instance instanceof PsrContainerInterface) {
            return static::$instance;
        }

        throw new \LogicException('container instance was not set');
    }

    public static function setContainer(CakeContainerInterface $container): void
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

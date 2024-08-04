<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\ServiceLocator;

use Cake\Core\ContainerInterface;
use Passchn\SimpleDI\Module\ServiceLocator\Exception\ContainerAlreadySet;
use Passchn\SimpleDI\Module\ServiceLocator\Exception\ContainerNotSet;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * The ServiceLocator pattern is an Anti-Pattern.
 *
 * This class should therefore only be used where proper Dependency Injection is not possible.
 *
 * Register the container in your Application::services hook or load the plugin to register it automatically.
 * The container will not be available before Application::services was called.
 *
 * @link https://de.wikipedia.org/wiki/Service-Locator
 */
class ServiceLocator
{
    protected static ?ContainerInterface $instance = null;

    public static function setContainer(ContainerInterface $container): void
    {
        if (static::$instance === null) {
            static::$instance = $container;
            return;
        }

        throw new ContainerAlreadySet(
            'The ServiceLocator has already bee registered.'
        );
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function get(string $id)
    {
        return static::getContainer()->get($id);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public static function getNew(string $id)
    {
        return static::getContainer()->getNew($id);
    }

    public static function has(string $id): bool
    {
        return static::getContainer()->has($id);
    }

    protected static function getContainer(): ContainerInterface
    {
        if (static::$instance instanceof ContainerInterface) {
            return static::$instance;
        }

        throw new ContainerNotSet(
            'Load the SimpleDI plugin or register the ServiceLocator in you Application class.'
        );
    }
}

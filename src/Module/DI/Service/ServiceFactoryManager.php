<?php

declare(strict_types=1);

namespace SimpleDI\Module\DI\Service;

use Cake\Core\ContainerInterface;
use SimpleDI\Module\DI\Service\Exception\ServiceNotCreated;

readonly class ServiceFactoryManager
{
    /**
     * @param array<class-string, class-string> $factories
     */
    public function __construct(protected array $factories)
    {
    }

    /**
     * @throws ServiceNotCreated
     */
    public function __invoke(ContainerInterface $container): void
    {
        $this->createServices($container);
    }

    /**
     * @throws ServiceNotCreated
     */
    protected function createServices(ContainerInterface $container): void
    {
        foreach ($this->factories as $serviceClass => $factory) {
            $this->addService($container, $serviceClass, $factory);
        }
    }

    /**
     * @throws ServiceNotCreated
     */
    protected function addService(ContainerInterface $container, string $serviceClass, string|callable $factory): void
    {
        if (!class_exists($serviceClass) && !interface_exists($serviceClass)) {
            throw new ServiceNotCreated(
                sprintf('Service class %s does not exist.', $serviceClass)
            );
        }

        $isCallableFactory = is_callable($factory);
        if (!$isCallableFactory && !class_exists($factory)) {
            throw new ServiceNotCreated(
                sprintf('Factory class %s does not exist.', $factory)
            );
        }

        $callableFactory = $isCallableFactory
            ? $factory
            : fn() => $this->createServiceWithFactory($container, $serviceClass, $factory);

        $container->add($serviceClass, $callableFactory);
    }

    /**
     * @throws ServiceNotCreated
     */
    protected function createServiceWithFactory(ContainerInterface $container, string $serviceClass, string $factoryClass): object
    {
        try {
            $factory = new $factoryClass();
        } catch (\Throwable $throwable) {
            throw new ServiceNotCreated(
                sprintf('Could not create factory %s', $factoryClass),
                previous: $throwable,
            );
        }
        try {
            $service = $factory($container);
        } catch (\Throwable $throwable) {
            throw new ServiceNotCreated(
                sprintf('Error while invoking factory %s: %s', $factoryClass, $throwable->getMessage()),
                previous: $throwable,
            );
        }

        if (!is_a($service, $serviceClass)) {
            throw new ServiceNotCreated(
                sprintf(
                    'Invoking factory %s lead to wrong object: %s instead of %s',
                    $factoryClass,
                    $service::class,
                    $serviceClass,
                ),
            );
        }

        return $service;
    }
}

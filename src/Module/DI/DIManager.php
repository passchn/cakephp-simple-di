<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\DI;

use Cake\Core\ContainerInterface;
use Passchn\SimpleDI\Module\DI\Exception\ServiceNotCreated;
use Passchn\SimpleDI\Module\DI\Factory\InvokableFactoryInterface;
use Passchn\SimpleDI\Module\Module\ModuleInterface;
use Passchn\SimpleDI\Module\Plugin\PluginInterface;

readonly class DIManager
{
    final public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    public static function create(ContainerInterface $container): static
    {
        return new static($container);
    }

    /**
     * Maps service classes to their factories
     *
     * @param array<class-string, class-string<InvokableFactoryInterface>|callable> $services
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addServices(array $services): static
    {
        foreach ($services as $serviceClass => $factory) {
            $this->addService($serviceClass, $factory);
        }

        return $this;
    }

    /**
     * @param list<class-string<PluginInterface>> $plugins
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addPlugins(array $plugins): static
    {
        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }

        return $this;
    }

    /**
     * @param class-string<PluginInterface> $plugin
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addPlugin(string $plugin): static
    {
        foreach ($plugin::modules() as $module) {
            $this->addModule($module);
        }

        return $this;
    }

    /**
     * @param list<class-string<ModuleInterface>> $modules
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addModules(array $modules): static
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }

        return $this;
    }

    /**
     * @param class-string<ModuleInterface> $module
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addModule(string $module): static
    {
        $this->addServices($module::services());

        return $this;
    }

    /**
     * @param class-string $serviceClass
     * @param class-string<InvokableFactoryInterface>|callable $factory
     * @return $this
     * @throws ServiceNotCreated
     */
    public function addService(string $serviceClass, string|callable $factory): static
    {
        if (!class_exists($serviceClass) && !interface_exists($serviceClass)) {
            throw new ServiceNotCreated(
                sprintf('Service class %s does not exist.', $serviceClass)
            );
        }

        $callableFactory = is_callable($factory)
            ? $factory
            : fn() => $this->createServiceWithFactory($this->container, $serviceClass, $factory);

        $this->container->add($serviceClass, $callableFactory);

        return $this;
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

        if (!is_callable($factory)) {
            throw new ServiceNotCreated(
                sprintf('Factory class %s is not callable.', $factory)
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

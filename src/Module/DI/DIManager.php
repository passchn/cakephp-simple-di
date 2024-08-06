<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\DI;

use Cake\Core\ContainerInterface;
use Passchn\SimpleDI\Module\DI\Exception\ServiceNotCreated;
use Passchn\SimpleDI\Module\DI\Factory\AbstractFactoryInterface;
use Passchn\SimpleDI\Module\DI\Factory\FactoryInterface;
use Passchn\SimpleDI\Module\DI\Factory\InvokableFactoryInterface;
use Passchn\SimpleDI\Module\Module\ModuleInterface;
use Passchn\SimpleDI\Module\Plugin\PluginInterface;
use ReflectionClass;

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
     * @param array<class-string, class-string<FactoryInterface>|callable> $services
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
     * @param class-string<FactoryInterface>|callable $factory
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

        if (is_callable($factory)) {
            $this->container->add($serviceClass, $factory);
            return $this;
        }

        $this->container->add(
            $serviceClass,
            fn() => $this->createServiceWithFactory($this->container, $serviceClass, $factory),
        );

        return $this;
    }

    /**
     * @template T
     * @param class-string<T> $serviceClass
     * @param class-string<FactoryInterface> $factoryClass
     * @return T
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

        $service = $this->createByFactoryInterface($container, $factory, $serviceClass);

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

    /**
     * @template T of object
     * @param class-string<T> $serviceClass
     * @return T
     * @throws ServiceNotCreated
     */
    protected function createByFactoryInterface(ContainerInterface $container, FactoryInterface $factory, string $serviceClass): object
    {
        $reflection = new ReflectionClass($factory);

        if ($reflection->implementsInterface(AbstractFactoryInterface::class)) {
            /**
             * @var AbstractFactoryInterface $factory
             */
            return $factory(
                $container,
                [
                    'requestedClass' => $serviceClass,
                ],
            );
        }

        if ($reflection->implementsInterface(InvokableFactoryInterface::class) || $reflection->implementsInterface(FactoryInterface::class)) {
            /**
             * @var InvokableFactoryInterface $factory
             */
            return $factory($container);
        }

        throw new ServiceNotCreated('invalid factory given');
    }
}

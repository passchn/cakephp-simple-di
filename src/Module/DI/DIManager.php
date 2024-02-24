<?php

declare(strict_types=1);

namespace SimpleDI\Module\DI;

use Cake\Core\ContainerInterface;

readonly class DIManager
{
    /**
     * @param array<class-string, array<class-string, class-string>> $factoriesConfig
     */
    public function __construct(
        protected array $factoriesConfig,
    )
    {
    }

    public function addDependencies(ContainerInterface $container): void
    {
        foreach ($this->factoriesConfig as $factoryClass => $config) {
            $factory = new $factoryClass($config);
            $factory($container);
        }
    }
}

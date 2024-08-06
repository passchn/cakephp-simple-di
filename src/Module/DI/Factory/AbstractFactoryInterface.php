<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\DI\Factory;

use Cake\Core\ContainerInterface;

interface AbstractFactoryInterface extends FactoryInterface
{
    /**
     * @template T of object
     * @param array{
     *     requestedClass: class-string<T>,
     * } $options
     * @return T
     */
    public function __invoke(ContainerInterface $container, array $options);
}

<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\DI\Factory;

use Cake\Core\ContainerInterface;

interface InvokableFactoryInterface extends FactoryInterface
{
    public function __invoke(ContainerInterface $container);
}

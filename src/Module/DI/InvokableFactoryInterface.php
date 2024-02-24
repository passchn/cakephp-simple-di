<?php

declare(strict_types=1);

namespace SimpleDI\Module\DI;

use Cake\Core\ContainerInterface;

interface InvokableFactoryInterface
{
    public function __invoke(ContainerInterface $container);
}

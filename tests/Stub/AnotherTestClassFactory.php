<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

use Cake\Core\ContainerInterface;
use Passchn\SimpleDI\Module\DI\Factory\InvokableFactoryInterface;

class AnotherTestClassFactory implements InvokableFactoryInterface
{
    public function __invoke(ContainerInterface $container): AnotherTestClass
    {
        return new AnotherTestClass('created by factory');
    }
}
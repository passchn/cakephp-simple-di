<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

use Cake\Core\ContainerInterface;
use Passchn\SimpleDI\Module\DI\Exception\ServiceNotCreated;
use Passchn\SimpleDI\Module\DI\Factory\AbstractFactoryInterface;
use Passchn\SimpleDI\Module\DI\Factory\FactoryInterface;
use Passchn\SimpleDI\Module\DI\Factory\InvokableFactoryInterface;

class AbstractTestFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): AnotherTestClass|YetAnotherTestClass
    {
        if ($options['requestedClass'] === AnotherTestClass::class) {
            return new AnotherTestClass('created by abstract factory');
        }

        if ($options['requestedClass'] === YetAnotherTestClass::class) {
            return new YetAnotherTestClass('created by abstract factory');
        }

        throw new ServiceNotCreated('unknown class');
    }
}
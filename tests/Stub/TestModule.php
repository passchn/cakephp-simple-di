<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

use Passchn\SimpleDI\Module\Module\ModuleInterface;

class TestModule implements ModuleInterface
{
    public static function services(): array
    {
        return [
            TestClass::class => fn () => new TestClass('created by module'),
            AnotherTestClass::class => AnotherTestClassFactory::class,
        ];
    }
}
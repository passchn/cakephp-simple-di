<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

use Passchn\SimpleDI\Module\Plugin\PluginInterface;

class TestPlugin implements PluginInterface
{
    public static function modules(): array
    {
        return [
            TestModule::class,
        ];
    }
}
<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\Module;

use Passchn\SimpleDI\Module\DI\Factory\InvokableFactoryInterface;

interface ModuleInterface
{
    /**
     * @return array<class-string, class-string<InvokableFactoryInterface>|callable>
     */
    public static function services(): array;
}
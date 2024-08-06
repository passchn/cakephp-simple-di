<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\Module;

use Passchn\SimpleDI\Module\DI\Factory\FactoryInterface;

interface ModuleInterface
{
    /**
     * @return array<class-string, class-string<FactoryInterface>|callable>
     */
    public static function services(): array;
}
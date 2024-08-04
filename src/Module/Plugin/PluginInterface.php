<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Module\Plugin;

use Passchn\SimpleDI\Module\Module\ModuleInterface;

interface PluginInterface
{
    /**
     * @return list<class-string<ModuleInterface>>
     */
    public static function modules(): array;
}
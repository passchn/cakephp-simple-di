<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

final readonly class YetAnotherTestClass
{
    public function __construct(
        public string $someProperty,
    ) {
    }
}
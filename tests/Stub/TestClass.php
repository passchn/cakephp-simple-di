<?php

declare(strict_types=1);

namespace Passchn\SimpleDI\Tests\Stub;

final readonly class TestClass
{
    public function __construct(
        public string $someProperty,
    ) {
    }
}
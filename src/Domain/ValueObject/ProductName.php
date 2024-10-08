<?php

declare(strict_types=1);

namespace VendingMachine\Domain\ValueObject;

readonly class ProductName
{
    public function __construct(
        private string $value,
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }
}

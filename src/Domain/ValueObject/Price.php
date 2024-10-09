<?php

declare(strict_types=1);

namespace VendingMachine\Domain\ValueObject;

readonly class Price
{
    public function __construct(
        private float $value,
    ) {
    }

    public function value(): float
    {
        return $this->value;
    }
}

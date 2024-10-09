<?php

declare(strict_types=1);

namespace VendingMachine\Domain\ValueObject;

readonly class Amount
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

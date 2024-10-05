<?php

namespace VendingMachine\Domain\ValueObject;

readonly class Price
{
    public function __construct(
        private float $value
    )
    {
    }

    public function value(): float
    {
        return $this->value;
    }
}
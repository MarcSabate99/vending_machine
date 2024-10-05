<?php

namespace VendingMachine\Domain\ValueObject;

readonly class Coin
{
    public function __construct(
        private float $value
    ) {
    }

    public function value(): float
    {
        return $this->value;
    }
}
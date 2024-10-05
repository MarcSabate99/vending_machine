<?php

namespace VendingMachine\Domain\ValueObject;

readonly class Quantity
{
    public function __construct(
        private int $value
    )
    {
    }

    public function value(): int
    {
        return $this->value;
    }
}
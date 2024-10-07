<?php

namespace VendingMachine\Domain\ValueObject;

class ProductId
{
    public function __construct(
        private int $value,
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }
}

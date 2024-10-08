<?php

namespace VendingMachine\Application\Command;

readonly class SetPriceCommand
{
    public function __construct(
        private float $price,
        private int $id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function price(): float
    {
        return $this->price;
    }
}

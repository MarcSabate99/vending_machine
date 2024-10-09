<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

readonly class InsertCoinCommand
{
    public function __construct(
        private string|float $quantity,
    ) {
    }

    public function quantity(): string|float
    {
        return $this->quantity;
    }
}

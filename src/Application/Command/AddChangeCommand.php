<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

readonly class AddChangeCommand
{
    public function __construct(
        private float $quantity,
    ) {
    }

    public function quantity(): float
    {
        return $this->quantity;
    }
}

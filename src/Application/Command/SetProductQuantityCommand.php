<?php

namespace VendingMachine\Application\Command;

readonly class SetProductQuantityCommand
{
    public function __construct(
        private int $quantity,
        private int $id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

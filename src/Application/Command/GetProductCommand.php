<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

readonly class GetProductCommand
{
    public function __construct(
        private int $quantity,
        private string $productName,
    ) {
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

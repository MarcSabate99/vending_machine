<?php

namespace VendingMachine\Application\Command;

readonly class AddProductCommand
{
    public function __construct(
        private string $productName,
        private int $quantity,
        private float $price,
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

    public function price(): float
    {
        return $this->price;
    }
}

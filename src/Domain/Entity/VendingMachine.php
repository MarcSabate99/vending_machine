<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Entity;

readonly class VendingMachine
{
    public function __construct(
        private array $products,
        private float $change,
        private float $insertedMoney,
    ) {
    }

    public function change(): float
    {
        return $this->change;
    }

    public function insertedMoney(): float
    {
        return $this->insertedMoney;
    }

    public function products(): array
    {
        return $this->products;
    }
}

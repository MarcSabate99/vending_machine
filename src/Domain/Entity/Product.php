<?php

namespace VendingMachine\Domain\Entity;

use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;
use VendingMachine\Domain\ValueObject\Price;

readonly class Product
{
    public function __construct(
        private ProductName $itemName,
        private Price       $price,
        private Quantity    $itemQuantity
    ) {
    }

    public function itemName(): ProductName
    {
        return $this->itemName;
    }

    public function itemQuantity(): Quantity
    {
        return $this->itemQuantity;
    }

    public function price(): Price
    {
        return $this->price;
    }
}
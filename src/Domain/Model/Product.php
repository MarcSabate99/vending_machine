<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Model;

use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

class Product
{
    public function __construct(
        private ProductName $itemName,
        private Price $price,
        private Quantity $itemQuantity,
        private ProductId $productId,
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

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function setItemQuantity(Quantity $itemQuantity): void
    {
        $this->itemQuantity = $itemQuantity;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}

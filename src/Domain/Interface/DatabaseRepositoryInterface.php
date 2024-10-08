<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Interface;

use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

interface DatabaseRepositoryInterface
{
    public function insertAmount(Amount $amount);

    public function returnInsertedCoins();

    public function addChange(Amount $amount);

    public function addProduct(Price $price, ProductName $productName, Quantity $quantity);

    public function listProducts(): array;

    public function setProductQuantity(Quantity $quantity, ProductId $productId);

    public function setProductPrice(Price $price, ProductId $productId);

    public function getProductByName(ProductName $productName): ?Product;

    public function sellProduct(Product $product, Quantity $quantity, Amount $change);

    public function getInsertedMoneyAndChange();
}

<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\Model\Product;

readonly class ProductMother
{
    public static function create(string $productName, float $price, int $quantity, int $productId): Product
    {
        return new Product(
            ProductNameMother::create($productName),
            PriceMother::create($price),
            QuantityMother::create($quantity),
            ProductIdMother::create($productId)
        );
    }
}

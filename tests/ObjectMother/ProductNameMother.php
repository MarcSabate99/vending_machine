<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\ProductName;

class ProductNameMother
{
    public static function create(string $value): ProductName
    {
        return new ProductName($value);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\ProductId;

readonly class ProductIdMother
{
    public static function create(int $value): ProductId
    {
        return new ProductId($value);
    }
}

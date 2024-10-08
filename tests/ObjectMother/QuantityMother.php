<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Quantity;

readonly class QuantityMother
{
    public static function create(int $value): Quantity
    {
        return new Quantity($value);
    }
}

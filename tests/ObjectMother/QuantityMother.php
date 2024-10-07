<?php

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Quantity;

class QuantityMother
{
    public static function create(int $value): Quantity
    {
        return new Quantity($value);
    }
}

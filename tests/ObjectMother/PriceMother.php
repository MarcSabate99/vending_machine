<?php

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Price;

class PriceMother
{
    public static function create(float $value): Price
    {
        return new Price($value);
    }
}

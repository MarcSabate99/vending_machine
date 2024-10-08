<?php

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Amount;

class AmountMother
{
    public static function create(float|string $value): Amount
    {
        return new Amount($value);
    }
}

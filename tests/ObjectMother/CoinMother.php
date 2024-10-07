<?php

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Coin;

class CoinMother
{
    public static function create(float|string $value): Coin
    {
        return new Coin($value);
    }
}

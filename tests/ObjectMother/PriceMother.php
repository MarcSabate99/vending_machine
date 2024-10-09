<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Price;

readonly class PriceMother
{
    public static function create(float $value): Price
    {
        return new Price($value);
    }
}

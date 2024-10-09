<?php

declare(strict_types=1);

namespace App\Tests\ObjectMother;

use VendingMachine\Domain\ValueObject\Amount;

readonly class AmountMother
{
    public static function create(float|string $value): Amount
    {
        return new Amount($value);
    }
}

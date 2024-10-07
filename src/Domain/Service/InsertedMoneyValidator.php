<?php

namespace VendingMachine\Domain\Service;

use VendingMachine\Domain\Exception\InvalidCoinProvided;

class InsertedMoneyValidator
{
    private const VALID_COINS = [0.05, 0.10, 0.25, 1];

    public function handle(float $quantity): void
    {
        if (!in_array($quantity, self::VALID_COINS)) {
            throw InvalidCoinProvided::ofInvalidNumber();
        }
    }
}

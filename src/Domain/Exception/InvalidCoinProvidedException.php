<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

class InvalidCoinProvidedException extends \Exception
{
    public static function ofInvalidNumber(array $validCoins)
    {
        return new self('Could not insert provided coin, valid coins: ' . implode(',', $validCoins));
    }
}

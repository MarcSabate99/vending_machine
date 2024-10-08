<?php

namespace VendingMachine\Domain\Exception;

use VendingMachine\Domain\ValueObject\Amount;

class NotEnoughMoneyInsertedException extends \Exception
{
    public static function of(Amount $currentAmount)
    {
        return new self('Not enough money inserted, currently inserted: ' . $currentAmount->value());
    }
}

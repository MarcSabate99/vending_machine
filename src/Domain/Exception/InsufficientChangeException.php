<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

class InsufficientChangeException extends \Exception
{
    public static function of(): InsufficientChangeException
    {
        return new self('Insufficient change in vending machine');
    }
}

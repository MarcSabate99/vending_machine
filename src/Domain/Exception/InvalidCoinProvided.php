<?php

namespace VendingMachine\Domain\Exception;

class InvalidCoinProvided extends \Exception
{
    public static function ofInvalidNumber()
    {
        return new self('Could not insert provided coin');
    }
}

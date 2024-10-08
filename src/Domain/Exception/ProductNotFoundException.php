<?php

namespace VendingMachine\Domain\Exception;

use VendingMachine\Domain\ValueObject\ProductName;

class ProductNotFoundException extends \Exception
{
    public static function of(ProductName $productName)
    {
        return new self('Product ' . $productName->value() . 'not found');
    }
}

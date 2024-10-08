<?php

namespace VendingMachine\Domain\Exception;

use VendingMachine\Domain\Model\Product;

class InsufficientMoneyException extends \Exception
{
    public static function of(Product $product)
    {
        return new self('Insufficient money to buy ' . $product->itemName()->value());
    }
}

<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

use VendingMachine\Domain\Model\Product;

class InsufficientStockException extends \Exception
{
    public static function of(Product $product)
    {
        return new self('Insufficient stock to buy ' . $product->itemName()->value());
    }
}

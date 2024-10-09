<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

use VendingMachine\Domain\ValueObject\ProductName;

class ProductWithNegativePriceException extends \Exception
{
    public static function of(ProductName $productName)
    {
        return new self('Product ' . $productName->value() . ' has negative price');
    }
}

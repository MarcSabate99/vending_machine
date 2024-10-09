<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Service;

use VendingMachine\Domain\Exception\InsufficientChangeException;
use VendingMachine\Domain\Exception\InsufficientMoneyException;
use VendingMachine\Domain\Exception\InsufficientStockException;
use VendingMachine\Domain\Exception\ProductWithNegativePriceException;
use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Quantity;

class GetProductValidator
{
    private const NO_COST = 0;

    public function handle(
        Product $product,
        Quantity $quantity,
        Amount $insertedMoney,
        Amount $currentChange,
        Amount $change,
    ): void {
        if (number_format($product->price()->value(), 2) < self::NO_COST) {
            throw ProductWithNegativePriceException::of($product->itemName());
        }

        if (number_format($product->price()->value() * $quantity->value(), 2) > number_format($insertedMoney->value(), 2)) {
            throw InsufficientMoneyException::of($product);
        }

        if ($product->itemQuantity()->value() < $quantity->value()) {
            throw InsufficientStockException::of($product);
        }

        if (number_format($currentChange->value(), 2) < number_format($change->value(), 2)) {
            throw InsufficientChangeException::of();
        }
    }
}

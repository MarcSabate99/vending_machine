<?php

namespace VendingMachine\Domain\Service;

use VendingMachine\Domain\Exception\InsufficientChangeException;
use VendingMachine\Domain\Exception\InsufficientMoneyException;
use VendingMachine\Domain\Exception\InsufficientStockException;
use VendingMachine\Domain\Exception\NotEnoughMoneyInsertedException;
use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Quantity;

class GetProductValidator
{
    private const NO_CHANGE = 0;

    public function handle(
        Product $product,
        Quantity $quantity,
        Amount $insertedMoney,
        Amount $currentChange,
        Amount $change,
    ): void {
        if (($product->price()->value() * $quantity->value()) > $insertedMoney->value()) {
            throw InsufficientMoneyException::of($product);
        }

        if ($product->itemQuantity()->value() < $quantity->value()) {
            throw InsufficientStockException::of($product);
        }

        if ($change->value() < self::NO_CHANGE) {
            throw NotEnoughMoneyInsertedException::of($insertedMoney);
        }

        if ($currentChange->value() < $change->value()) {
            throw InsufficientChangeException::of();
        }
    }
}

<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Entity;

use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

class VendingMachine
{
    public function __construct(
        private array $products,
        private Amount $change,
        private Amount $insertedMoney,
    ) {
    }

    public static function from(array $vendingMachine): VendingMachine
    {
        $products = [];
        foreach ($vendingMachine['products'] as $product) {
            $products[] = new Product(
                new ProductName($product['name']),
                new Price((float) $product['price']),
                new Quantity((int) $product['quantity']),
                new ProductId((int) $product['id'])
            );
        }

        return new self(
            $products,
            new Amount((float) $vendingMachine['change']),
            new Amount((float) $vendingMachine['insertedMoney'])
        );
    }

    public function change(): Amount
    {
        return $this->change;
    }

    public function insertedMoney(): Amount
    {
        return $this->insertedMoney;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function setChange(Amount $change): void
    {
        $this->change = $change;
    }

    public function setInsertedMoney(Amount $insertedMoney): void
    {
        $this->insertedMoney = $insertedMoney;
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function toArray(): array
    {
        $products = [];

        /**
         * @var Product $p
         */
        foreach ($this->products as $p) {
            $products[] = [
                'name'     => $p->itemName()->value(),
                'quantity' => $p->itemQuantity()->value(),
                'price'    => $p->price()->value(),
                'id'       => $p->productId()->value(),
            ];
        }

        return [
            'products'      => $products,
            'change'        => $this->change->value(),
            'insertedMoney' => $this->insertedMoney->value(),
        ];
    }
}

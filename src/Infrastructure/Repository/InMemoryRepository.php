<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\Repository;

use VendingMachine\Domain\Entity\VendingMachine;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

readonly class InMemoryRepository implements DatabaseRepositoryInterface
{
    private const BASE_ID  = 1;
    private const NO_MONEY = 0;

    public function __construct(
        private string $databasePath,
    ) {
    }

    public function insertAmount(Amount $amount): void
    {
        $vendingMachine = $this->getVendingMachine();
        $vendingMachine->setInsertedMoney(
            new Amount($vendingMachine->insertedMoney()->value() + $amount->value())
        );
        $this->save($vendingMachine);
    }

    public function returnInsertedCoins(): void
    {
        $vendingMachine = $this->getVendingMachine();
        $vendingMachine->setInsertedMoney(new Amount(self::NO_MONEY));
        $this->save($vendingMachine);
    }

    public function addChange(Amount $amount): void
    {
        $vendingMachine = $this->getVendingMachine();
        $vendingMachine->setChange(
            new Amount($vendingMachine->change()->value() + $amount->value())
        );
        $this->save($vendingMachine);
    }

    public function addProduct(Price $price, ProductName $productName, Quantity $quantity): void
    {
        $vendingMachine    = $this->getVendingMachine();
        $currentProducts   = $vendingMachine->products();
        $currentProducts[] = new Product(
            $productName,
            $price,
            $quantity,
            new ProductId($this->generateId())
        );
        $vendingMachine->setProducts($currentProducts);
        $this->save($vendingMachine);
    }

    public function listProducts(): array
    {
        $vendingMachine = $this->getVendingMachine();
        $products       = [];

        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            $products[] = [
                'name'     => $product->itemName()->value(),
                'quantity' => $product->itemQuantity()->value(),
                'price'    => $product->price()->value(),
                'id'       => $product->productId()->value(),
            ];
        }

        return $products;
    }

    public function setProductQuantity(Quantity $quantity, ProductId $productId): void
    {
        $vendingMachine = $this->getVendingMachine();
        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            if ($product->productId()->value() !== $productId->value()) {
                continue;
            }
            $product->setItemQuantity($quantity);
            break;
        }

        $this->save($vendingMachine);
    }

    public function setProductPrice(Price $price, ProductId $productId): void
    {
        $vendingMachine = $this->getVendingMachine();
        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            if ($product->productId()->value() !== $productId->value()) {
                continue;
            }
            $product->setPrice($price);
            break;
        }

        $this->save($vendingMachine);
    }

    public function getProductByName(ProductName $productName): ?Product
    {
        $vendingMachine = $this->getVendingMachine();
        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            if ($product->itemName()->value() !== $productName->value()) {
                continue;
            }

            return $product;
        }

        return null;
    }

    public function sellProduct(Product $product, Quantity $quantity, Amount $change): void
    {
        $vendingMachine = $this->getVendingMachine();
        /**
         * @var Product $p
         */
        foreach ($vendingMachine->products() as $p) {
            if ($p->itemName()->value() !== $product->itemName()->value()) {
                continue;
            }

            $p->setItemQuantity(
                new Quantity($p->itemQuantity()->value() - $quantity->value())
            );
        }

        $vendingMachine->setChange(
            new Amount(
                (float) number_format($vendingMachine->change()->value() - $change->value(), 2, '.', '')
            )
        );

        $vendingMachine->setInsertedMoney(
            new Amount(0)
        );

        $this->save($vendingMachine);
    }

    public function getInsertedMoneyAndChange(): array
    {
        $vendingMachine = $this->getVendingMachine();

        return [
            'change'        => $vendingMachine->change(),
            'insertedMoney' => $vendingMachine->insertedMoney(),
        ];
    }

    private function getVendingMachine(): VendingMachine
    {
        $databaseFile   = file_get_contents($this->databasePath);
        $vendingMachine = json_decode($databaseFile, true);
        if (null === $vendingMachine) {
            throw new \Exception('No database provided');
        }

        return VendingMachine::from($vendingMachine);
    }

    private function save(VendingMachine $vendingMachine): void
    {
        $vendingMachineArray = $vendingMachine->toArray();
        $vendingMachineJson  = json_encode($vendingMachineArray, JSON_PRETTY_PRINT);
        file_put_contents($this->databasePath, $vendingMachineJson);
    }

    private function generateId(): int
    {
        $vendingMachine = $this->getVendingMachine();
        $products       = $vendingMachine->products();
        if (!empty($products)) {
            /**
             * @var Product $lastProduct
             */
            $lastProduct = end($products);

            return $lastProduct->productId()->value() + self::BASE_ID;
        }

        return self::BASE_ID;
    }
}

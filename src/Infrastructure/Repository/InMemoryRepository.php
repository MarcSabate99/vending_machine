<?php

namespace VendingMachine\Infrastructure\Repository;

use VendingMachine\Domain\Entity\VendingMachine;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
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
        $this->createDb();
        $vendingMachineJson = $this->getData();
        $vendingMachineJson['insertedMoney'] += $amount->value();
        $this->save($vendingMachineJson);
    }

    private function getData()
    {
        $databaseFile = file_get_contents($this->databasePath);

        return json_decode($databaseFile, true);
    }

    private function createDb(): void
    {
        if (file_exists($this->databasePath)) {
            return;
        }

        $vendingMachine     = new VendingMachine([], self::NO_MONEY, self::NO_MONEY);
        $vendingMachineJson = json_encode([
            'products'      => $vendingMachine->products(),
            'change'        => $vendingMachine->change(),
            'insertedMoney' => $vendingMachine->insertedMoney(),
        ], JSON_PRETTY_PRINT);

        file_put_contents($this->databasePath, $vendingMachineJson);
    }

    private function save(array $vendingMachineJson): void
    {
        $vendingMachineJson = json_encode([
            'products'      => $vendingMachineJson['products'],
            'change'        => $vendingMachineJson['change'],
            'insertedMoney' => $vendingMachineJson['insertedMoney'],
        ], JSON_PRETTY_PRINT);
        file_put_contents($this->databasePath, $vendingMachineJson);
    }

    public function returnInsertedCoins(): void
    {
        $this->createDb();
        $vendingMachineJson                  = $this->getData();
        $vendingMachineJson['insertedMoney'] = self::NO_MONEY;
        $this->save($vendingMachineJson);
    }

    public function addChange(Amount $amount): void
    {
        $this->createDb();
        $vendingMachineJson = $this->getData();
        $vendingMachineJson['change'] += $amount->value();
        $this->save($vendingMachineJson);
    }

    public function addProduct(Price $price, ProductName $productName, Quantity $quantity): void
    {
        $this->createDb();
        $vendingMachineJson               = $this->getData();
        $vendingMachineJson['products'][] = [
            'name'     => $productName->value(),
            'quantity' => $quantity->value(),
            'price'    => $price->value(),
            'id'       => $this->generateId(),
        ];
        $this->save($vendingMachineJson);
    }

    private function generateId()
    {
        $vendingMachineJson = $this->getData();
        if (!empty($vendingMachineJson['products'])) {
            $lastProduct = end($vendingMachineJson['products']);

            return $lastProduct['id'] + self::BASE_ID;
        }

        return self::BASE_ID;
    }

    public function listProducts(): array
    {
        $vendingMachineJson = $this->getData();

        return $vendingMachineJson['products'];
    }

    public function setProductQuantity(Quantity $quantity, ProductId $productId): void
    {
        $vendingMachineJson = $this->getData();
        foreach ($vendingMachineJson['products'] as &$product) {
            if ($product['id'] !== $productId->value()) {
                continue;
            }

            $product['quantity'] = $quantity->value();
        }

        $this->save($vendingMachineJson);
    }

    public function setProductPrice(Price $price, ProductId $productId): void
    {
        $vendingMachineJson = $this->getData();
        foreach ($vendingMachineJson['products'] as &$product) {
            if ($product['id'] !== $productId->value()) {
                continue;
            }

            $product['price'] = $price->value();
        }

        $this->save($vendingMachineJson);
    }
}

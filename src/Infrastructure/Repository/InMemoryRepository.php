<?php

namespace VendingMachine\Infrastructure\Repository;

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
        $vendingMachineJson = $this->getData();
        $vendingMachineJson['insertedMoney'] += $amount->value();
        $this->save($vendingMachineJson);
    }

    public function returnInsertedCoins(): void
    {
        $vendingMachineJson                  = $this->getData();
        $vendingMachineJson['insertedMoney'] = self::NO_MONEY;
        $this->save($vendingMachineJson);
    }

    public function addChange(Amount $amount): void
    {
        $vendingMachineJson = $this->getData();
        $vendingMachineJson['change'] += $amount->value();
        $this->save($vendingMachineJson);
    }

    public function addProduct(Price $price, ProductName $productName, Quantity $quantity): void
    {
        $vendingMachineJson               = $this->getData();
        $vendingMachineJson['products'][] = [
            'name'     => $productName->value(),
            'quantity' => $quantity->value(),
            'price'    => $price->value(),
            'id'       => $this->generateId(),
        ];
        $this->save($vendingMachineJson);
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

    public function getProductByName(ProductName $productName): ?Product
    {
        $vendingMachineJson = $this->getData();
        foreach ($vendingMachineJson['products'] as $product) {
            if ($product['name'] !== $productName->value()) {
                continue;
            }

            return new Product(
                new ProductName($product['name']),
                new Price($product['price']),
                new Quantity($product['quantity'])
            );
        }

        return null;
    }

    public function sellProduct(Product $product, Quantity $quantity, Amount $change): void
    {
        $vendingMachineJson = $this->getData();
        foreach ($vendingMachineJson['products'] as &$productArray) {
            if ($productArray['name'] !== $product->itemName()->value()) {
                continue;
            }

            $productArray['quantity'] -= $quantity->value();
        }

        $vendingMachineJson['change']        = (float) number_format($vendingMachineJson['change'] - $change->value(), 2, '.', '');
        $vendingMachineJson['insertedMoney'] = 0;
        $this->save($vendingMachineJson);
    }

    public function getInsertedMoneyAndChange(): array
    {
        $vendingMachineJson = $this->getData();

        return [
            'change'        => new Amount($vendingMachineJson['change']),
            'insertedMoney' => new Amount($vendingMachineJson['insertedMoney']),
        ];
    }

    private function getData(): ?array
    {
        $databaseFile = file_get_contents($this->databasePath);

        return json_decode($databaseFile, true);
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

    private function generateId()
    {
        $vendingMachineJson = $this->getData();
        if (!empty($vendingMachineJson['products'])) {
            $lastProduct = end($vendingMachineJson['products']);

            return $lastProduct['id'] + self::BASE_ID;
        }

        return self::BASE_ID;
    }
}

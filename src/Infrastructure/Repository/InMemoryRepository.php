<?php

namespace VendingMachine\Infrastructure\Repository;

use VendingMachine\Domain\Entity\VendingMachine;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\Coin;

readonly class InMemoryRepository implements DatabaseRepositoryInterface
{
    public function __construct(
        private string $databasePath,
    ) {
    }

    public function insertCoin(Coin $coin): void
    {
        $this->createDb();
        $vendingMachineJson = $this->getData();
        $vendingMachineJson['insertedMoney'] += $coin->value();
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

        $vendingMachine     = new VendingMachine([], 0, 0);
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
        $vendingMachineJson['insertedMoney'] = 0;
        $this->save($vendingMachineJson);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\integration\Module;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\Entity\VendingMachine;
use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class VendingMachineIntegrationModule extends TestCase
{
    public const DATABASE_PATH = 'tests/db/vending_machine.json';

    public static function getVendingMachine(): VendingMachine
    {
        $databaseFile   = file_get_contents(self::DATABASE_PATH);
        $vendingMachine = json_decode($databaseFile, true);
        if (null === $vendingMachine) {
            throw new \Exception('No database provided');
        }

        return VendingMachine::from($vendingMachine);
    }

    public static function createDb(): void
    {
        $vendingMachine     = new VendingMachine([], new Amount(0), new Amount(0));
        $vendingMachineJson = json_encode([
            'products'      => $vendingMachine->products(),
            'change'        => $vendingMachine->change(),
            'insertedMoney' => $vendingMachine->insertedMoney(),
        ], JSON_PRETTY_PRINT);

        file_put_contents(self::DATABASE_PATH, $vendingMachineJson);
    }

    public static function getInMemoryRepository(): InMemoryRepository
    {
        return new InMemoryRepository(self::DATABASE_PATH);
    }

    public static function thereIsAProductInDb(Product $product): void
    {
        self::getInMemoryRepository()->addProduct($product->price(), $product->itemName(), $product->itemQuantity());
    }

    public static function thereIsChange(Amount $changeAmount): void
    {
        self::getInMemoryRepository()->addChange($changeAmount);
    }

    public static function thereIsInsertedMoney(Amount $insertedMoney): void
    {
        self::getInMemoryRepository()->insertAmount($insertedMoney);
    }

    public static function theChangeShouldBe(Amount $change): void
    {
        $data = self::getInMemoryRepository()->getInsertedMoneyAndChange();
        Assert::assertEquals($change->value(), $data['change']->value());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\e2e\Module;

use VendingMachine\Domain\Entity\VendingMachine;
use VendingMachine\Domain\ValueObject\Amount;

class VendingMachineModule
{
    public const DATABASE_PATH = 'tests/db/vending_machine.json';

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

    public static function getVendingMachine(): VendingMachine
    {
        $databaseFile   = file_get_contents(self::DATABASE_PATH);
        $vendingMachine = json_decode($databaseFile, true);
        if (null === $vendingMachine) {
            throw new \Exception('No database provided');
        }

        return VendingMachine::from($vendingMachine);
    }
}

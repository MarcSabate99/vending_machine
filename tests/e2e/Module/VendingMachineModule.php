<?php

namespace App\Tests\e2e\Module;

use VendingMachine\Domain\Entity\VendingMachine;

class VendingMachineModule
{
    public const DATABASE_PATH = 'tests/db/vending_machine.json';

    public static function createDb(): void
    {
        $vendingMachine     = new VendingMachine([], 0, 0);
        $vendingMachineJson = json_encode([
            'products'      => $vendingMachine->products(),
            'change'        => $vendingMachine->change(),
            'insertedMoney' => $vendingMachine->insertedMoney(),
        ], JSON_PRETTY_PRINT);

        file_put_contents(self::DATABASE_PATH, $vendingMachineJson);
    }

    public function getData()
    {
        $content = file_get_contents(self::DATABASE_PATH);

        return json_decode($content, true);
    }
}

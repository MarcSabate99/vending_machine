<?php

namespace VendingMachine\Domain\Interface;

use VendingMachine\Domain\ValueObject\Coin;

interface DatabaseRepositoryInterface
{
    public function insertCoin(Coin $coin);

    public function returnInsertedCoins();
}

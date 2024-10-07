<?php

namespace VendingMachine\Domain\Interface;

use VendingMachine\Domain\ValueObject\Amount;

interface DatabaseRepositoryInterface
{
    public function insertAmount(Amount $amount);

    public function returnInsertedCoins();

    public function addChange(Amount $amount);
}

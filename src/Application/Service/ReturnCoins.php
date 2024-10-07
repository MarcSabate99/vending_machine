<?php

namespace VendingMachine\Application\Service;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;

class ReturnCoins
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(): void
    {
        $this->databaseRepository->returnInsertedCoins();
    }
}

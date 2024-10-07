<?php

namespace VendingMachine\Application\Service;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;

readonly class ReturnCoins
{
    public function __construct(
        private DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(): void
    {
        $this->databaseRepository->returnInsertedCoins();
    }
}

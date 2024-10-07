<?php

namespace VendingMachine\Application\Service;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;

class ListProducts
{
    public function __construct(
        private DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(): array
    {
        return $this->databaseRepository->listProducts();
    }
}

<?php

declare(strict_types=1);

namespace VendingMachine\Application\Service;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;

readonly class ListProducts
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

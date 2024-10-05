<?php

namespace VendingMachine\Infrastructure\Repository;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;

readonly class InMemoryRepository implements DatabaseRepositoryInterface
{
    public function __construct(
        private string $database
    ) {
    }
}
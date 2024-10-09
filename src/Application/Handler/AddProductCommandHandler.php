<?php

declare(strict_types=1);

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\AddProductCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

readonly class AddProductCommandHandler
{
    public function __construct(
        private DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(AddProductCommand $command): void
    {
        $this->databaseRepository->addProduct(
            new Price($command->price()),
            new ProductName($command->productName()),
            new Quantity($command->quantity())
        );
    }
}

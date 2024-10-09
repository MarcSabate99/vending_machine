<?php

declare(strict_types=1);

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\SetProductQuantityCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\Quantity;

readonly class SetProductQuantityCommandHandler
{
    public function __construct(
        private DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(SetProductQuantityCommand $command): void
    {
        $this->databaseRepository->setProductQuantity(
            new Quantity($command->quantity()),
            new ProductId($command->id())
        );
    }
}

<?php

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\SetProductQuantityCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\ProductId;
use VendingMachine\Domain\ValueObject\Quantity;

class SetProductQuantityCommandHandler
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
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

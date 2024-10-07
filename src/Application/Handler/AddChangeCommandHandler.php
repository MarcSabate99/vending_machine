<?php

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\AddChangeCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\Amount;

class AddChangeCommandHandler
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(AddChangeCommand $command): void
    {
        $this->databaseRepository->addChange(
            new Amount($command->quantity())
        );
    }
}

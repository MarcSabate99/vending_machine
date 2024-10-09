<?php

declare(strict_types=1);

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\SetPriceCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\ValueObject\Price;
use VendingMachine\Domain\ValueObject\ProductId;

class SetPriceCommandHandler
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(SetPriceCommand $command): void
    {
        $this->databaseRepository->setProductPrice(
            new Price($command->price()),
            new ProductId($command->id())
        );
    }
}

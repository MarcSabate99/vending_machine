<?php

declare(strict_types=1);

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\InsertedMoneyValidator;
use VendingMachine\Domain\ValueObject\Amount;

readonly class InsertCoinCommandHandler
{
    public function __construct(
        private DatabaseRepositoryInterface $databaseRepository,
        private InsertedMoneyValidator $insertedMoneyValidator,
    ) {
    }

    public function handle(InsertCoinCommand $command): void
    {
        if (!is_float($command->quantity()) && str_contains($command->quantity(), ',')) {
            $values     = explode(',', $command->quantity());
            $finalValue = 0;
            foreach ($values as $value) {
                $castedValue = (float) $value;
                $this->insertedMoneyValidator->handle($castedValue);
                $finalValue += $castedValue;
            }

            $finalValue = number_format($finalValue, 2, '.', '');
            $this->databaseRepository->insertAmount(
                new Amount((float) $finalValue)
            );

            return;
        }

        $quantity = (float) $command->quantity();
        $this->insertedMoneyValidator->handle($quantity);
        $finalValue = number_format($quantity, 2, '.', '');

        $this->databaseRepository->insertAmount(
            new Amount((float) $finalValue)
        );
    }
}

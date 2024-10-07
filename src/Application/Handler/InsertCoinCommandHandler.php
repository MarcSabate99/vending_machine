<?php

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\InsertedMoneyValidator;
use VendingMachine\Domain\ValueObject\Coin;

class InsertCoinCommandHandler
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
        private readonly InsertedMoneyValidator $insertedMoneyValidator,
    ) {
    }

    public function handle(InsertCoinCommand $command): void
    {
        if (str_contains($command->quantity(), ',')) {
            $values     = explode(',', $command->quantity());
            $finalValue = 0;
            foreach ($values as $value) {
                $castedValue = (float) $value;
                $this->insertedMoneyValidator->handle($castedValue);
                $finalValue += $castedValue;
            }

            $finalValue = number_format($finalValue, 2, '.', '');
            $this->databaseRepository->insertCoin(
                new Coin($finalValue)
            );

            return;
        }

        $castedValue = (float) $command->quantity();
        $this->insertedMoneyValidator->handle($castedValue);
        $finalValue = number_format($castedValue, 2, '.', '');

        $this->databaseRepository->insertCoin(
            new Coin($finalValue)
        );
    }
}

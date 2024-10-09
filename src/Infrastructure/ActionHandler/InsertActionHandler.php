<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Handler\InsertCoinCommandHandler;

readonly class InsertActionHandler
{
    public function __construct(
        private InsertCoinCommandHandler $handler,
    ) {
    }

    public function handle(): void
    {
        echo 'Insert quantity: ';
        $handle   = fopen('php://stdin', 'r');
        $quantity = trim(fgets($handle));
        try {
            $this->handler->handle(new InsertCoinCommand($quantity));
            echo "Inserted\n";
        } catch (\Throwable $throwable) {
            echo $throwable->getMessage() . "\n";
        }
    }
}

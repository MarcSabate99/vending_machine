<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler;

use VendingMachine\Application\Service\ReturnCoins;

readonly class ReturnMoneyActionHandler
{
    public function __construct(
        private ReturnCoins $returnCoins,
    ) {
    }

    public function handle(): void
    {
        $this->returnCoins->handle();
        echo "Money returned\n";
    }
}

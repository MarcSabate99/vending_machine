<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler\Service;

use VendingMachine\Application\Command\AddChangeCommand;
use VendingMachine\Application\Handler\AddChangeCommandHandler;

readonly class AddChangeServiceHandler
{
    public function __construct(
        private AddChangeCommandHandler $addChangeCommandHandler,
    ) {
    }

    public function handle(): void
    {
        echo 'Insert the change: ';
        $handle   = fopen('php://stdin', 'r');
        $quantity = trim(fgets($handle));
        while (!is_numeric($quantity)) {
            echo 'Insert the change: ';
            $handle   = fopen('php://stdin', 'r');
            $quantity = trim(fgets($handle));
        }
        $this->addChangeCommandHandler->handle(new AddChangeCommand((float) $quantity));
        echo "Change added\n";
    }
}

<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler;

use VendingMachine\Application\Command\GetProductCommand;
use VendingMachine\Application\Handler\GetProductCommandHandler;

readonly class GetActionHandler
{
    public function __construct(
        private GetProductCommandHandler $productCommandHandler,
    ) {
    }

    public function handle(): void
    {
        echo 'Insert the quantity and the product name, example -> (10,SODA): ';
        $handle  = fopen('php://stdin', 'r');
        $getData = trim(fgets($handle));
        $getData = explode(',', $getData);
        while (count($getData) < 2) {
            echo "Provide a valid input\n";
            echo 'Insert the quantity and the product name, example -> (10,SODA): ';
            $handle  = fopen('php://stdin', 'r');
            $getData = trim(fgets($handle));
            $getData = explode(',', $getData);
        }

        while (!is_numeric($getData[0])) {
            echo "Provide a valid input\n";
            echo 'Insert the quantity and the product name, example -> (10,SODA): ';
            $handle  = fopen('php://stdin', 'r');
            $getData = trim(fgets($handle));
            $getData = explode(',', $getData);
        }

        try {
            $returnedAmount = $this->productCommandHandler->handle(
                new GetProductCommand((int) $getData[0], $getData[1])
            );
            echo 'Sold product: ' . $getData[1] . "\n";
            echo 'Returned money: ' . $returnedAmount->value() . "\n";
        } catch (\Throwable $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}

<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler\Service;

use VendingMachine\Application\Command\AddProductCommand;
use VendingMachine\Application\Handler\AddProductCommandHandler;

readonly class AddProductServiceHandler
{
    public function __construct(
        private AddProductCommandHandler $addProductCommandHandler,
    ) {
    }

    public function handle(): void
    {
        echo 'Insert the total elements, price and name following this format -> example: (10,0.45,Bread): ';
        $handle            = fopen('php://stdin', 'r');
        $insertProductData = trim(fgets($handle));
        $insertProductData = explode(',', $insertProductData);
        while (count($insertProductData) < 3) {
            echo "Provide a valid input\n";
            echo 'Insert the total elements, price and name following this format -> example: (10,0.45,Bread): ';
            $handle            = fopen('php://stdin', 'r');
            $insertProductData = trim(fgets($handle));
            $insertProductData = explode(',', $insertProductData);
        }

        while (!is_numeric($insertProductData[0]) || !is_numeric($insertProductData[1])) {
            echo "Provide a valid input\n";
            echo 'Insert the total elements, price and name following this format -> example: (10,0.45,Bread): ';
            $handle            = fopen('php://stdin', 'r');
            $insertProductData = trim(fgets($handle));
            $insertProductData = explode(',', $insertProductData);
        }

        $this->addProductCommandHandler->handle(new AddProductCommand(
            $insertProductData[2],
            (int) $insertProductData[0],
            (float) $insertProductData[1],
        ));
        echo "Product added\n";
    }
}

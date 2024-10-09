<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler\Service;

use VendingMachine\Application\Command\SetPriceCommand;
use VendingMachine\Application\Command\SetProductQuantityCommand;
use VendingMachine\Application\Handler\SetPriceCommandHandler;
use VendingMachine\Application\Handler\SetProductQuantityCommandHandler;
use VendingMachine\Application\Service\ListProducts;

readonly class ModifyProductServiceHandler
{
    private const QUANTITY = 'quantity';
    private const PRICE    = 'price';
    private const CANCEL   = 'cancel';

    public function __construct(
        private ListProducts $listProducts,
        private SetProductQuantityCommandHandler $setProductQuantityCommandHandler,
        private SetPriceCommandHandler $setPriceCommandHandler,
    ) {
    }

    public function handle(): string
    {
        $products     = $this->listProducts->handle();
        $availableIds = [];
        echo "Select the product: \n";
        foreach ($products as $product) {
            echo '[' . $product['id'] . '] ' . $product['name'] . "\n";
            $availableIds[] = $product['id'];
        }
        echo 'Enter the id: ';
        $handle = fopen('php://stdin', 'r');
        $id     = trim(fgets($handle));

        while (!in_array($id, $availableIds)) {
            echo 'Enter the id: ';
            $handle = fopen('php://stdin', 'r');
            $id     = trim(fgets($handle));
        }
        echo 'What do you want to modify? (quantity, price, cancel): ';
        $handle     = fopen('php://stdin', 'r');
        $modifyWhat = trim(fgets($handle));
        while ('quantity' !== $modifyWhat && 'price' !== $modifyWhat && 'cancel' !== $modifyWhat) {
            echo 'What do you want to modify? (quantity, price, cancel): ';
            $handle     = fopen('php://stdin', 'r');
            $modifyWhat = trim(fgets($handle));
        }

        switch ($modifyWhat) {
            case self::QUANTITY:
                echo 'Enter the quantity: ';
                $handle   = fopen('php://stdin', 'r');
                $quantity = trim(fgets($handle));
                while (!is_numeric($quantity)) {
                    echo 'Enter the quantity: ';
                    $handle   = fopen('php://stdin', 'r');
                    $quantity = trim(fgets($handle));
                }
                $this->setProductQuantityCommandHandler->handle(new SetProductQuantityCommand(
                    (int) $quantity,
                    (int) $id
                ));
                echo "Quantity modified\n";
                break;
            case self::PRICE:
                echo 'Enter the price: ';
                $handle = fopen('php://stdin', 'r');
                $price  = trim(fgets($handle));
                while (!is_numeric($price)) {
                    echo 'Enter the price: ';
                    $handle = fopen('php://stdin', 'r');
                    $price  = trim(fgets($handle));
                }
                $this->setPriceCommandHandler->handle(new SetPriceCommand(
                    (float) $price,
                    (int) $id
                ));
                echo "Price modified\n";
                break;
            case self::CANCEL:
                return 'exit';
        }

        return '';
    }
}

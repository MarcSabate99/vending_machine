<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler;

use VendingMachine\Infrastructure\ActionHandler\Service\AddChangeServiceHandler;
use VendingMachine\Infrastructure\ActionHandler\Service\ProductsServiceHandler;

readonly class ServiceActionHandler
{
    private const CHANGE   = 'change';
    private const PRODUCTS = 'products';
    private const EXIT     = 'exit';

    public function __construct(
        private AddChangeServiceHandler $addChangeServiceHandler,
        private ProductsServiceHandler $productsServiceHandler,
    ) {
    }

    public function handle(): void
    {
        echo 'Choose an action (change, products, exit): ';
        $handle        = fopen('php://stdin', 'r');
        $serviceAction = trim(fgets($handle));
        while ('exit' !== $serviceAction && 'change' !== $serviceAction && 'products' !== $serviceAction) {
            echo 'Choose an action (change, products, exit): ';
            $handle        = fopen('php://stdin', 'r');
            $serviceAction = trim(fgets($handle));
        }

        switch ($serviceAction) {
            case self::CHANGE:
                $this->addChangeServiceHandler->handle();
                break;
            case self::PRODUCTS:
                $this->productsServiceHandler->handle();
                break;
            case self::EXIT:
            default:
                break;
        }
    }
}

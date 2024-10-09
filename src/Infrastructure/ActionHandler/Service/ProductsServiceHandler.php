<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure\ActionHandler\Service;

readonly class ProductsServiceHandler
{
    private const ADD    = 'add';
    private const MODIFY = 'modify';
    private const CANCEL = 'cancel';

    public function __construct(
        private AddProductServiceHandler $addProductServiceHandler,
        private ModifyProductServiceHandler $modifyProductServiceHandler,
    ) {
    }

    public function handle(): string
    {
        echo 'Choose an action (add, modify, cancel): ';
        $handle        = fopen('php://stdin', 'r');
        $productAction = trim(fgets($handle));
        switch ($productAction) {
            case self::ADD:
                $this->addProductServiceHandler->handle();
                break;
            case self::MODIFY:
                $this->modifyProductServiceHandler->handle();
                break;
            case self::CANCEL:
                return 'exit';
                break;
            default:
                break;
        }

        return '';
    }
}

<?php

namespace VendingMachine\Application\Handler;

use VendingMachine\Application\Command\GetProductCommand;
use VendingMachine\Domain\Exception\ProductNotFoundException;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\GetProduct;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\ProductName;
use VendingMachine\Domain\ValueObject\Quantity;

class GetProductCommandHandler
{
    public function __construct(
        private readonly DatabaseRepositoryInterface $databaseRepository,
        private readonly GetProduct $getProduct,
    ) {
    }

    public function handle(GetProductCommand $command): Amount
    {
        $productName = new ProductName($command->productName());
        $product     = $this->databaseRepository->getProductByName(
            $productName
        );

        if (null === $product) {
            throw ProductNotFoundException::of($productName);
        }

        $vendingMachineData = $this->databaseRepository->getInsertedMoneyAndChange();

        return $this->getProduct->handle(
            $product,
            new Quantity($command->quantity()),
            $vendingMachineData['insertedMoney'],
            $vendingMachineData['change']
        );
    }
}

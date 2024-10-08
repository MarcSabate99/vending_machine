<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Service;

use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Model\Product;
use VendingMachine\Domain\ValueObject\Amount;
use VendingMachine\Domain\ValueObject\Quantity;

readonly class GetProduct
{
    public function __construct(
        private GetProductValidator $getProductValidator,
        private DatabaseRepositoryInterface $databaseRepository,
    ) {
    }

    public function handle(
        Product $product,
        Quantity $quantity,
        Amount $insertedMoney,
        Amount $currentChange,
    ): Amount {
        $change = new Amount($insertedMoney->value() - ($product->price()->value() * $quantity->value()));
        $this->getProductValidator->handle(
            $product,
            $quantity,
            $insertedMoney,
            $currentChange,
            $change
        );
        $this->databaseRepository->sellProduct($product, $quantity, $change);

        return new Amount((float) number_format($change->value(), 2, '.', ''));
    }
}

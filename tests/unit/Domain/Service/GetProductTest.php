<?php

namespace App\Tests\unit\Domain\Service;

use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\ProductMother;
use App\Tests\ObjectMother\QuantityMother;
use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\GetProduct;
use VendingMachine\Domain\Service\GetProductValidator;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class GetProductTest extends TestCase
{
    private GetProduct $getProduct;
    private GetProductValidator $getProductValidator;
    private DatabaseRepositoryInterface $databaseRepository;

    public function testSuccessHandle()
    {
        $product       = ProductMother::create('Example', 0.5, 1);
        $quantity      = QuantityMother::create(1);
        $insertedMoney = AmountMother::create(4);
        $currentChange = AmountMother::create(10);

        $change = AmountMother::create($insertedMoney->value() - ($product->price()->value() * $quantity->value()));

        $this->getProductValidator
            ->expects($this->once())
            ->method('handle')
            ->with(
                $product,
                $quantity,
                $insertedMoney,
                $currentChange,
                $change
            );

        $this->databaseRepository
            ->expects($this->once())
            ->method('sellProduct');

        $result = $this->getProduct->handle(
            $product,
            $quantity,
            $insertedMoney,
            $currentChange,
        );

        $this->assertEquals($change, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getProductValidator = $this->createMock(GetProductValidator::class);
        $this->databaseRepository  = $this->createMock(InMemoryRepository::class);
        $this->getProduct          = new GetProduct(
            $this->getProductValidator,
            $this->databaseRepository
        );
    }
}

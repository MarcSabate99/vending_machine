<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\Handler;

use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\ProductMother;
use App\Tests\ObjectMother\ProductNameMother;
use PHPUnit\Framework\TestCase;
use VendingMachine\Application\Command\GetProductCommand;
use VendingMachine\Application\Handler\GetProductCommandHandler;
use VendingMachine\Domain\Exception\ProductNotFoundException;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\GetProduct;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class GetProductCommandHandlerTest extends TestCase
{
    private GetProductCommandHandler $commandHandler;
    private DatabaseRepositoryInterface $databaseRepository;
    private GetProduct $getProduct;

    public function testHandleWithNullProduct()
    {
        $this->databaseRepository
            ->expects($this->once())
            ->method('getProductByName')
            ->with(ProductNameMother::create('Non existent product'))
            ->willReturn(null);

        $this->getProduct
            ->expects($this->never())
            ->method('handle');

        $this->expectException(ProductNotFoundException::class);

        $this->commandHandler->handle(
            new GetProductCommand(1, 'Non existent product')
        );
    }

    public function testHandleWithProduct()
    {
        $this->databaseRepository
            ->expects($this->once())
            ->method('getProductByName')
            ->with(ProductNameMother::create('Existent product'))
            ->willReturn(ProductMother::create('Existent product', 1.50, 1));

        $this->databaseRepository
            ->expects($this->once())
            ->method('getInsertedMoneyAndChange')
            ->willReturn([
                'insertedMoney' => AmountMother::create(0),
                'change'        => AmountMother::create(1),
            ]);

        $this->getProduct
            ->expects($this->once())
            ->method('handle');

        $this->commandHandler->handle(
            new GetProductCommand(1, 'Existent product')
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseRepository = $this->createMock(InMemoryRepository::class);
        $this->getProduct         = $this->createMock(GetProduct::class);
        $this->commandHandler     = new GetProductCommandHandler(
            $this->databaseRepository,
            $this->getProduct
        );
    }
}

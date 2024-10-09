<?php

declare(strict_types=1);

namespace App\Tests\integration\Domain\Service;

use App\Tests\integration\Module\VendingMachineIntegrationModule;
use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\ProductMother;
use VendingMachine\Application\Command\GetProductCommand;
use VendingMachine\Application\Handler\GetProductCommandHandler;
use VendingMachine\Domain\Exception\InsufficientChangeException;
use VendingMachine\Domain\Exception\InsufficientMoneyException;
use VendingMachine\Domain\Exception\InsufficientStockException;
use VendingMachine\Domain\Exception\ProductWithNegativePriceException;
use VendingMachine\Domain\Service\GetProduct;
use VendingMachine\Domain\Service\GetProductValidator;

class GetProductCommandHandlerTest extends VendingMachineIntegrationModule
{
    private GetProductCommandHandler $getProductCommandHandler;

    public function testZeroQuantity()
    {
        $product = ProductMother::create('Example', 50, 10, 1);

        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(100));
        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(0, 'Example')
            );

            $this->assertEquals(100, $change->value());
            self::theChangeShouldBe(AmountMother::create(0));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testNegativeQuantity()
    {
        $product = ProductMother::create('Example', 50, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(InsufficientChangeException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(-1, 'Example')
        );
    }

    public function testZeroInsertedMoney()
    {
        $product = ProductMother::create('Example', 50, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(0));

        $this->expectException(InsufficientMoneyException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testNegativeInsertedMoney()
    {
        $product = ProductMother::create('Example', 50, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(-50));

        $this->expectException(InsufficientMoneyException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testInsertedMoneyEqualProductCost()
    {
        $product = ProductMother::create('Example', 50, 5, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(50));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(1, 'Example')
            );

            $this->assertEquals(0, $change->value());
            self::theChangeShouldBe(AmountMother::create(100));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testInsufficientChangeAvailable()
    {
        $product = ProductMother::create('Example', 60, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(5));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(InsufficientChangeException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testProductStockIsZero()
    {
        $product = ProductMother::create('Example', 30, 0, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(50));
        self::thereIsInsertedMoney(AmountMother::create(50));

        $this->expectException(InsufficientStockException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testBuyMoreItemsThanAvailableStock()
    {
        $product = ProductMother::create('Example', 25, 2, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(InsufficientStockException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(3, 'Example')
        );
    }

    public function testNegativeProductPrice()
    {
        $product = ProductMother::create('Example', -50, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(ProductWithNegativePriceException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testNegativeChangeResult()
    {
        $product = ProductMother::create('Example', 150, 5, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(50));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(InsufficientMoneyException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testWithProductPriceFloat()
    {
        $product = ProductMother::create('Example', 99.99, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(100.00));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(1, 'Example')
            );

            $this->assertEquals(0.01, $change->value());
            self::theChangeShouldBe(AmountMother::create(99.99));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testWithProductPriceDiff()
    {
        $product = ProductMother::create('Example', 50.01, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(5));
        self::thereIsInsertedMoney(AmountMother::create(50));

        $this->expectException(InsufficientMoneyException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(1, 'Example')
        );
    }

    public function testExactChangeWithDecimalProductPrice()
    {
        $product = ProductMother::create('Example', 75.75, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(10.25));
        self::thereIsInsertedMoney(AmountMother::create(86));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(1, 'Example')
            );

            $this->assertEquals(10.25, $change->value());
            self::theChangeShouldBe(AmountMother::create(0));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testMultipleQuantitiesWithDecimals()
    {
        $product = ProductMother::create('Example', 33.33, 5, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(40));
        self::thereIsInsertedMoney(AmountMother::create(100));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(2, 'Example')
            );

            $this->assertEquals(33.34, $change->value());
            self::theChangeShouldBe(AmountMother::create(6.66));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testMultipleQuantitiesWithDecimalsWithInsufficientChange()
    {
        $product = ProductMother::create('Example', 33.33, 5, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(20));
        self::thereIsInsertedMoney(AmountMother::create(100));

        $this->expectException(InsufficientChangeException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(2, 'Example')
        );
    }

    public function testPriceWithFloatDecimal()
    {
        $product = ProductMother::create('Example', 29.99, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(100));
        self::thereIsInsertedMoney(AmountMother::create(90));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(3, 'Example')
            );

            $this->assertEquals(0.03, $change->value());
            self::theChangeShouldBe(AmountMother::create(99.97));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testPriceWithFloatDecimalWithoutEnoughChange()
    {
        $product = ProductMother::create('Example', 29.99, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(0.02));
        self::thereIsInsertedMoney(AmountMother::create(90));

        $this->expectException(InsufficientChangeException::class);
        $this->getProductCommandHandler->handle(
            new GetProductCommand(3, 'Example')
        );
    }

    public function testNoChangeReturnMatchByProductPrice()
    {
        $product = ProductMother::create('Example', 12.34, 10, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(50));
        self::thereIsInsertedMoney(AmountMother::create(37.02));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(3, 'Example')
            );

            $this->assertEquals(0, $change->value());
            self::theChangeShouldBe(AmountMother::create(50));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testWithMoreThan2DecimalsAfterCalculation()
    {
        $product = ProductMother::create('Example', 0.65, 5, 1);
        self::thereIsAProductInDb($product);
        self::thereIsChange(AmountMother::create(2.65));
        self::thereIsInsertedMoney(AmountMother::create(1.95));

        try {
            $change = $this->getProductCommandHandler->handle(
                new GetProductCommand(3, 'Example')
            );

            $this->assertEquals(0, $change->value());
            self::theChangeShouldBe(AmountMother::create(2.65));
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!file_exists(self::DATABASE_PATH)) {
            $this->createDb();
        }

        $inMemoryRepository = self::getInMemoryRepository();

        $getProduct = new GetProduct(
            new GetProductValidator(),
            $inMemoryRepository
        );

        $this->getProductCommandHandler = new GetProductCommandHandler(
            $inMemoryRepository,
            $getProduct
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink(self::DATABASE_PATH);
    }
}

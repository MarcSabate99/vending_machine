<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Service;

use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\ProductMother;
use App\Tests\ObjectMother\QuantityMother;
use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\Exception\InsufficientChangeException;
use VendingMachine\Domain\Exception\InsufficientMoneyException;
use VendingMachine\Domain\Exception\InsufficientStockException;
use VendingMachine\Domain\Service\GetProductValidator;

class GetProductValidatorTest extends TestCase
{
    private GetProductValidator $getProductValidator;

    public function testInsufficientMoney()
    {
        $this->expectException(InsufficientMoneyException::class);

        $this->getProductValidator->handle(
            ProductMother::create('Example', 0.5, 2, 1),
            QuantityMother::create(1),
            AmountMother::create(0.2),
            AmountMother::create(2),
            AmountMother::create(0)
        );
    }

    public function testInsufficientStock()
    {
        $this->expectException(InsufficientStockException::class);

        $this->getProductValidator->handle(
            ProductMother::create('Example', 0.5, 1, 1),
            QuantityMother::create(2),
            AmountMother::create(2),
            AmountMother::create(2),
            AmountMother::create(0)
        );
    }

    public function testNotEnoughMoneyInserted()
    {
        try {
            $this->getProductValidator->handle(
                ProductMother::create('Example', 0.5, 1, 1),
                QuantityMother::create(1),
                AmountMother::create(2),
                AmountMother::create(2),
                AmountMother::create(-1)
            );
            $this->expectNotToPerformAssertions();
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function testInsufficientChange()
    {
        $this->expectException(InsufficientChangeException::class);

        $this->getProductValidator->handle(
            ProductMother::create('Example', 0.5, 1, 1),
            QuantityMother::create(1),
            AmountMother::create(2),
            AmountMother::create(2),
            AmountMother::create(3)
        );
    }

    public function testHandleSuccess()
    {
        try {
            $this->getProductValidator->handle(
                ProductMother::create('Example', 0.5, 1, 1),
                QuantityMother::create(1),
                AmountMother::create(2),
                AmountMother::create(2),
                AmountMother::create(1)
            );
            $this->expectNotToPerformAssertions();
        } catch (\Throwable $exception) {
            $this->fail($exception->getMessage());
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getProductValidator = new GetProductValidator();
    }
}

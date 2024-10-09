<?php

declare(strict_types=1);

namespace App\Tests\integration\Infrastructure\Repository;

use App\Tests\integration\Module\VendingMachineIntegrationModule;
use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\PriceMother;
use App\Tests\ObjectMother\ProductIdMother;
use App\Tests\ObjectMother\ProductMother;
use App\Tests\ObjectMother\ProductNameMother;
use App\Tests\ObjectMother\QuantityMother;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class InMemoryRepositoryTest extends VendingMachineIntegrationModule
{
    private InMemoryRepository $inMemoryRepository;

    public function testInsertCoin()
    {
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(1, $vendingMachine->insertedMoney()->value());
    }

    public function testInsertCoinAndReturn()
    {
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $vendingMachine = $this->getVendingMachine();

        $this->assertNotNull($vendingMachine);
        $this->assertEquals(1, $vendingMachine->insertedMoney()->value());
        $this->inMemoryRepository->returnInsertedCoins();

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(0, $vendingMachine->insertedMoney()->value());
    }

    public function testAddChange()
    {
        $this->inMemoryRepository->addChange(AmountMother::create(60));
        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(60, $vendingMachine->change()->value());
    }

    public function testAddProducts()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );
        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertCount(1, $vendingMachine->products());
        $this->assertEquals('Bread', $vendingMachine->products()[0]->itemName()->value());
        $this->assertEquals(15, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(0.65, $vendingMachine->products()[0]->price()->value());

        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.05),
            ProductNameMother::create('Cherry'),
            QuantityMother::create(35)
        );

        $vendingMachine = $this->getVendingMachine();

        $this->assertNotNull($vendingMachine);
        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertEquals(2, $vendingMachine->products()[1]->productId()->value());
        $this->assertCount(2, $vendingMachine->products());
        $this->assertEquals('Cherry', $vendingMachine->products()[1]->itemName()->value());
        $this->assertEquals(35, $vendingMachine->products()[1]->itemQuantity()->value());
        $this->assertEquals(0.05, $vendingMachine->products()[1]->price()->value());
    }

    public function testSetProductPrice()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.23),
            ProductNameMother::create('Example'),
            QuantityMother::create(25)
        );

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertCount(2, $vendingMachine->products());

        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertEquals('Bread', $vendingMachine->products()[0]->itemName()->value());
        $this->assertEquals(15, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(0.65, $vendingMachine->products()[0]->price()->value());

        $this->assertEquals(2, $vendingMachine->products()[1]->productId()->value());
        $this->assertEquals('Example', $vendingMachine->products()[1]->itemName()->value());
        $this->assertEquals(25, $vendingMachine->products()[1]->itemQuantity()->value());
        $this->assertEquals(0.23, $vendingMachine->products()[1]->price()->value());

        $this->inMemoryRepository->setProductPrice(PriceMother::create(0.20), ProductIdMother::create(1));

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);

        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertEquals('Bread', $vendingMachine->products()[0]->itemName()->value());
        $this->assertEquals(15, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(0.20, $vendingMachine->products()[0]->price()->value());
    }

    public function testSetProductQuantity()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.23),
            ProductNameMother::create('Example'),
            QuantityMother::create(25)
        );
        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertCount(2, $vendingMachine->products());

        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertEquals('Bread', $vendingMachine->products()[0]->itemName()->value());
        $this->assertEquals(15, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(0.65, $vendingMachine->products()[0]->price()->value());

        $this->assertEquals(2, $vendingMachine->products()[1]->productId()->value());
        $this->assertEquals('Example', $vendingMachine->products()[1]->itemName()->value());
        $this->assertEquals(25, $vendingMachine->products()[1]->itemQuantity()->value());
        $this->assertEquals(0.23, $vendingMachine->products()[1]->price()->value());

        $this->inMemoryRepository->setProductQuantity(QuantityMother::create(12), ProductIdMother::create(1));

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(1, $vendingMachine->products()[0]->productId()->value());
        $this->assertEquals('Bread', $vendingMachine->products()[0]->itemName()->value());
        $this->assertEquals(12, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(0.65, $vendingMachine->products()[0]->price()->value());
    }

    public function testGetProductByName()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );

        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Water'),
            QuantityMother::create(2)
        );

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertCount(2, $vendingMachine->products());

        $product = $this->inMemoryRepository->getProductByName(ProductNameMother::create('Bread'));

        $this->assertNotNull($product);
        $this->assertEquals(0.65, $product->price()->value());
        $this->assertEquals(15, $product->itemQuantity()->value());

        $product = $this->inMemoryRepository->getProductByName(ProductNameMother::create('Water'));

        $this->assertNotNull($product);
        $this->assertEquals(0.65, $product->price()->value());
        $this->assertEquals(2, $product->itemQuantity()->value());
    }

    public function testGetInsertedMoneyAndChange()
    {
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertEquals(3, $vendingMachine->insertedMoney()->value());

        $this->inMemoryRepository->addChange(AmountMother::create(60));
        $vendingMachine = $this->getVendingMachine();
        $this->assertEquals(60, $vendingMachine->change()->value());

        $insertedMoneyAndChange = $this->inMemoryRepository->getInsertedMoneyAndChange();
        $this->assertNotNull($insertedMoneyAndChange);
        $this->assertIsArray($insertedMoneyAndChange);
        $this->assertEquals(3, $insertedMoneyAndChange['insertedMoney']->value());
        $this->assertEquals(60, $insertedMoneyAndChange['change']->value());
    }

    public function testSellProduct()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(2),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );

        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $this->inMemoryRepository->addChange(AmountMother::create(5));

        $vendingMachine = $this->getVendingMachine();
        $this->assertNotNull($vendingMachine);
        $this->assertCount(1, $vendingMachine->products());

        $this->inMemoryRepository->sellProduct(
            ProductMother::create('Bread', 2, 15, 1),
            QuantityMother::create(1),
            AmountMother::create(2)
        );

        $vendingMachine = $this->getVendingMachine();

        $this->assertNotNull($vendingMachine);
        $this->assertCount(1, $vendingMachine->products());
        $this->assertEquals(14, $vendingMachine->products()[0]->itemQuantity()->value());
        $this->assertEquals(3, $vendingMachine->change()->value());
        $this->assertEquals(0, $vendingMachine->insertedMoney()->value());
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!file_exists(self::DATABASE_PATH)) {
            $this->createDb();
        }

        $this->inMemoryRepository = self::getInMemoryRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink(self::DATABASE_PATH);
    }
}

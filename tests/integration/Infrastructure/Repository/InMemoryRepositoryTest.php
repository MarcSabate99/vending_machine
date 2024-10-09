<?php

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
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['insertedMoney']);
    }

    public function testInsertCoinAndReturn()
    {
        $this->inMemoryRepository->insertAmount(AmountMother::create(1));
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['insertedMoney']);
        $this->inMemoryRepository->returnInsertedCoins();
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(0, $data['insertedMoney']);
    }

    public function testAddChange()
    {
        $this->inMemoryRepository->addChange(AmountMother::create(60));
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(60, $data['change']);
    }

    public function testAddProducts()
    {
        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.65),
            ProductNameMother::create('Bread'),
            QuantityMother::create(15)
        );
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertCount(1, $data['products']);
        $this->assertEquals('Bread', $data['products'][0]['name']);
        $this->assertEquals(15, $data['products'][0]['quantity']);
        $this->assertEquals(0.65, $data['products'][0]['price']);

        $this->inMemoryRepository->addProduct(
            PriceMother::create(0.05),
            ProductNameMother::create('Cherry'),
            QuantityMother::create(35)
        );

        $data = $this->readDatabase();

        $this->assertNotNull($data);
        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertEquals(2, $data['products'][1]['id']);
        $this->assertCount(2, $data['products']);
        $this->assertEquals('Cherry', $data['products'][1]['name']);
        $this->assertEquals(35, $data['products'][1]['quantity']);
        $this->assertEquals(0.05, $data['products'][1]['price']);
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
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertCount(2, $data['products']);

        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertEquals('Bread', $data['products'][0]['name']);
        $this->assertEquals(15, $data['products'][0]['quantity']);
        $this->assertEquals(0.65, $data['products'][0]['price']);

        $this->assertEquals(2, $data['products'][1]['id']);
        $this->assertEquals('Example', $data['products'][1]['name']);
        $this->assertEquals(25, $data['products'][1]['quantity']);
        $this->assertEquals(0.23, $data['products'][1]['price']);

        $this->inMemoryRepository->setProductPrice(PriceMother::create(0.20), ProductIdMother::create(1));

        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertEquals('Bread', $data['products'][0]['name']);
        $this->assertEquals(15, $data['products'][0]['quantity']);
        $this->assertEquals(0.20, $data['products'][0]['price']);
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
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertCount(2, $data['products']);

        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertEquals('Bread', $data['products'][0]['name']);
        $this->assertEquals(15, $data['products'][0]['quantity']);
        $this->assertEquals(0.65, $data['products'][0]['price']);

        $this->assertEquals(2, $data['products'][1]['id']);
        $this->assertEquals('Example', $data['products'][1]['name']);
        $this->assertEquals(25, $data['products'][1]['quantity']);
        $this->assertEquals(0.23, $data['products'][1]['price']);

        $this->inMemoryRepository->setProductQuantity(QuantityMother::create(12), ProductIdMother::create(1));

        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['products'][0]['id']);
        $this->assertEquals('Bread', $data['products'][0]['name']);
        $this->assertEquals(12, $data['products'][0]['quantity']);
        $this->assertEquals(0.65, $data['products'][0]['price']);
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

        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertCount(2, $data['products']);

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

        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(3, $data['insertedMoney']);

        $this->inMemoryRepository->addChange(AmountMother::create(60));
        $data = $this->readDatabase();
        $this->assertEquals(60, $data['change']);

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

        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertCount(1, $data['products']);

        $this->inMemoryRepository->sellProduct(
            ProductMother::create('Bread', 2, 15),
            QuantityMother::create(1),
            AmountMother::create(2)
        );

        $data = $this->readDatabase();

        $this->assertNotNull($data);
        $this->assertCount(1, $data['products']);
        $this->assertEquals(14, $data['products'][0]['quantity']);
        $this->assertEquals(3, $data['change']);
        $this->assertEquals(0, $data['insertedMoney']);
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

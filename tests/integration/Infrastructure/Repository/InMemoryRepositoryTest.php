<?php

namespace App\Tests\integration\Infrastructure\Repository;

use App\Tests\ObjectMother\AmountMother;
use App\Tests\ObjectMother\PriceMother;
use App\Tests\ObjectMother\ProductIdMother;
use App\Tests\ObjectMother\ProductNameMother;
use App\Tests\ObjectMother\QuantityMother;
use PHPUnit\Framework\TestCase;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class InMemoryRepositoryTest extends TestCase
{
    private InMemoryRepository $inMemoryRepository;
    private const DATABASE_PATH = 'tests/db/vending_machine.json';

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(self::DATABASE_PATH)) {
            unlink(self::DATABASE_PATH);
        }

        $this->inMemoryRepository = new InMemoryRepository(self::DATABASE_PATH);
    }

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

    private function readDatabase(): ?array
    {
        $content = file_get_contents(self::DATABASE_PATH);

        return json_decode($content, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink(self::DATABASE_PATH);
    }
}

<?php

namespace App\Tests\integration\Infrastructure\Repository;

use App\Tests\ObjectMother\CoinMother;
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
        $this->inMemoryRepository->insertCoin(CoinMother::create(1));
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['insertedMoney']);
    }

    public function testInsertCoinAndReturn()
    {
        $this->inMemoryRepository->insertCoin(CoinMother::create(1));
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(1, $data['insertedMoney']);
        $this->inMemoryRepository->returnInsertedCoins();
        $data = $this->readDatabase();
        $this->assertNotNull($data);
        $this->assertEquals(0, $data['insertedMoney']);
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

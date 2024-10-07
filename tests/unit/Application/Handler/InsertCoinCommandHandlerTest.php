<?php

namespace App\Tests\unit\Application\Handler;

use App\Tests\ObjectMother\CoinMother;
use PHPUnit\Framework\TestCase;
use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Handler\InsertCoinCommandHandler;
use VendingMachine\Domain\Exception\InvalidCoinProvided;
use VendingMachine\Domain\Interface\DatabaseRepositoryInterface;
use VendingMachine\Domain\Service\InsertedMoneyValidator;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;

class InsertCoinCommandHandlerTest extends TestCase
{
    private InsertCoinCommandHandler $coinCommandHandler;
    private DatabaseRepositoryInterface $databaseRepository;
    private InsertedMoneyValidator $insertedMoneyValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseRepository     = $this->createMock(InMemoryRepository::class);
        $this->insertedMoneyValidator = new InsertedMoneyValidator();

        $this->coinCommandHandler = new InsertCoinCommandHandler(
            $this->databaseRepository,
            $this->insertedMoneyValidator
        );
    }

    public function testWithMultipleValidCoins()
    {
        $this->databaseRepository
            ->expects($this->once())
            ->method('insertCoin')
            ->with(CoinMother::create(1.40));

        $this->coinCommandHandler->handle(
            new InsertCoinCommand('1,0.05,0.10,0.25')
        );
    }

    public function testWithMultipleProvidedButOneInvalidCoin()
    {
        $this->expectException(InvalidCoinProvided::class);

        $this->databaseRepository
            ->expects($this->never())
            ->method('insertCoin');

        $this->coinCommandHandler->handle(
            new InsertCoinCommand('1,0.05,0.12,0.25')
        );
    }

    public function testWithOneValidCoinAsString()
    {
        $this->databaseRepository
            ->expects($this->once())
            ->method('insertCoin')
            ->with(CoinMother::create(1));

        $this->coinCommandHandler->handle(
            new InsertCoinCommand('1')
        );
    }

    public function testWithOneInvalidCoinAsString()
    {
        $this->expectException(InvalidCoinProvided::class);

        $this->databaseRepository
            ->expects($this->never())
            ->method('insertCoin');

        $this->coinCommandHandler->handle(
            new InsertCoinCommand('0.24')
        );
    }

    public function testWithOneValidCoinAsFloat()
    {
        $this->databaseRepository
            ->expects($this->once())
            ->method('insertCoin')
            ->with(CoinMother::create(0.25));

        $this->coinCommandHandler->handle(
            new InsertCoinCommand(0.25)
        );
    }

    public function testWithOneInvalidCoinAsFloat()
    {
        $this->expectException(InvalidCoinProvided::class);

        $this->databaseRepository
            ->expects($this->never())
            ->method('insertCoin');

        $this->coinCommandHandler->handle(
            new InsertCoinCommand(0.31)
        );
    }
}

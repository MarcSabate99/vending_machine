<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\Exception\InvalidCoinProvidedException;
use VendingMachine\Domain\Service\InsertedMoneyValidator;

class InsertedMoneyValidatorTest extends TestCase
{
    private InsertedMoneyValidator $insertedMoneyValidator;

    #[DataProvider('invalidInsertedMoney')]
    public function testWithInvalidInsertedMoney(float $val)
    {
        $this->expectException(InvalidCoinProvidedException::class);
        $this->insertedMoneyValidator->handle($val);
    }

    #[DataProvider('validInsertedMoney')]
    public function testWithValidInsertedMoney(float $val)
    {
        $this->expectNotToPerformAssertions();
        $this->insertedMoneyValidator->handle($val);
    }

    public static function validInsertedMoney(): array
    {
        return [
            'test 0.05'  => [0.05],
            'test 0.10'  => [0.10],
            'test 0.1'   => [0.1],
            'test 0.25'  => [0.25],
            'test 0.250' => [0.250],
            'test 1'     => [1],
        ];
    }

    public static function invalidInsertedMoney(): array
    {
        return [
            'not supported'             => [123],
            'left corner case of 0.05'  => [0.04],
            'right corner case of 0.05' => [0.06],
            'left corner case of 0.10'  => [0.09],
            'right corner case of 0.10' => [0.11],
            'left corner case of 0.25'  => [0.24],
            'right corner case of 0.25' => [0.26],
            'left corner case of 1'     => [0.99],
            'right corner case of 1'    => [1.01],
            'random number'             => [23],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertedMoneyValidator = new InsertedMoneyValidator();
    }
}

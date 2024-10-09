<?php

declare(strict_types=1);

namespace App\Tests\e2e\Context;

use App\Tests\e2e\Module\VendingMachineModule;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\FeatureScope;
use Behat\Behat\Hook\Scope\ScenarioScope;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use VendingMachine\Domain\Model\Product;

class VendingMachineContext extends VendingMachineModule implements Context
{
    private Process $process;
    private InputStream $input;

    public function __construct()
    {
        $this->input   = new InputStream();
        $this->process = new Process(['php', 'index.php']);
        $this->process->setTimeout(5);
        $this->process->setInput($this->input);
        $this->process->start();
    }

    /**
     * @When /^I wait to "([^"]*)" and I input "([^"]*)"$/
     */
    public function iInputAndWaitTo(string $question, string $input): void
    {
        $this->process->waitUntil(function ($type, $output) use ($question): bool {
            return str_contains($output, $question);
        });

        $this->input->write($input . "\n");
    }

    /**
     * @When /^I input "([^"]*)"$/
     */
    public function iInput(string $input): void
    {
        $this->input->write($input . "\n");
    }

    /**
     * @Then /^I should see "([^"]*)" and continue$/
     */
    public function iShouldSeeAndContinue($expectedOutput): void
    {
        $this->process->waitUntil(function ($type, $output) use ($expectedOutput): bool {
            return str_contains($output, $expectedOutput);
        });
    }

    /**
     * @Then /^I should see "([^"]*)" and ends$/
     */
    public function iShouldSeeAndEnds($expectedOutput): void
    {
        $this->process->waitUntil(function ($type, $output) use ($expectedOutput): bool {
            return str_contains($output, $expectedOutput);
        });

        $this->input->close();
        $this->process->stop();
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" as inserted money$/
     */
    public function theVendingMachineShouldHaveAsInsertedMoney(string $expectedInsertedMoney): void
    {
        $vendingMachine = $this->getVendingMachine();
        Assert::assertEquals((float) $expectedInsertedMoney, $vendingMachine->insertedMoney()->value());
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" as change$/
     */
    public function theVendingMachineShouldHaveAsChange(string $change): void
    {
        $vendingMachine = $this->getVendingMachine();
        Assert::assertEquals((float) $change, $vendingMachine->change()->value());
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" product with quantity "([^"]*)" and price "([^"]*)"$/
     */
    public function theVendingMachineShouldHaveProductWithQuantityAndPrice(string $productName, string $quantity, string $price): void
    {
        $vendingMachine = $this->getVendingMachine();
        $found          = false;
        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            if ($product->itemName()->value() === $productName) {
                Assert::assertEquals((int) $quantity, (int) $product->itemQuantity()->value());
                Assert::assertEquals((float) $price, (float) $product->price()->value());
                $found = true;
                break;
            }
        }

        if (!$found) {
            Assert::fail('Product no exists');
        }
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" as quantity of "([^"]*)"$/
     */
    public function theVendingMachineShouldHaveAsQuantityOf(string $quantity, string $productName): void
    {
        $vendingMachine = $this->getVendingMachine();
        $found          = false;
        /**
         * @var Product $product
         */
        foreach ($vendingMachine->products() as $product) {
            if ($product->itemName()->value() === $productName) {
                Assert::assertEquals((int) $quantity, $product->itemQuantity()->value());
                $found = true;
                break;
            }
        }

        if (!$found) {
            Assert::fail('Product no exists');
        }
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" as a change$/
     */
    public function theVendingMachineShouldHaveAsAChange($expectedChange): void
    {
        $vendingMachine = $this->getVendingMachine();
        Assert::assertEquals((float) $expectedChange, (float) $vendingMachine->change()->value());
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(ScenarioScope $scope): void
    {
        if (file_exists(self::DATABASE_PATH)) {
            unlink(self::DATABASE_PATH);
            self::createDb();
        }
    }

    /**
     * @AfterFeature
     */
    public static function afterFeature(FeatureScope $scope): void
    {
        if (file_exists(self::DATABASE_PATH)) {
            unlink(self::DATABASE_PATH);
            self::createDb();
        }
    }

    /**
     * @BeforeFeature
     */
    public static function beforeFeature(FeatureScope $scope): void
    {
        $_ENV['test'] = true;
        if (file_exists(self::DATABASE_PATH)) {
            unlink(self::DATABASE_PATH);
            self::createDb();
        }
    }
}

<?php

namespace App\Tests\e2e\Context;

use App\Tests\e2e\Module\VendingMachineModule;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\FeatureScope;
use Behat\Behat\Hook\Scope\ScenarioScope;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

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
        $vendingMachine = $this->getData();
        Assert::assertEquals((float) $expectedInsertedMoney, (float) $vendingMachine['insertedMoney']);
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" as change$/
     */
    public function theVendingMachineShouldHaveAsChange(string $change): void
    {
        $vendingMachine = $this->getData();
        Assert::assertEquals((float) $change, (float) $vendingMachine['change']);
    }

    /**
     * @Then /^the vending machine should have "([^"]*)" product with quantity "([^"]*)" and price "([^"]*)"$/
     */
    public function theVendingMachineShouldHaveProductWithQuantityAndPrice(string $productName, string $quantity, string $price): void
    {
        $vendingMachine = $this->getData();
        $found          = false;
        foreach ($vendingMachine['products'] as $product) {
            if ($product['name'] === $productName) {
                Assert::assertEquals((float) $quantity, (float) $product['quantity']);
                Assert::assertEquals((float) $price, (float) $product['price']);
                $found = true;
                break;
            }
        }

        if (!$found) {
            Assert::fail('Product no exists');
        }
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

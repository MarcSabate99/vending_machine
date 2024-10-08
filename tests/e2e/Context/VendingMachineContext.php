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
        $this->process->setTimeout(10);
        $this->process->setInput($this->input);
        $this->process->start();
    }

    /**
     * @When /^I input "([^"]*)" and wait to "([^"]*)"$/
     */
    public function iInputAndWaitTo(string $input, string $question): void
    {
        $this->process->waitUntil(function ($type, $output) use ($question): bool {
            return $output === $question;
        });

        $this->input->write($input . "\n");
    }

    /**
     * @Then I should see :expectedOutput
     */
    public function iShouldSee(string $expectedOutput): void
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
        }
        self::createDb();
    }
}

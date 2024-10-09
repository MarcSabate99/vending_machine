<?php

declare(strict_types=1);

namespace VendingMachine;

use VendingMachine\Infrastructure\ActionHandler;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;
use VendingMachine\Infrastructure\System\Container;

readonly class Kernel
{
    private Container $container;
    private const DB_PATH      = 'db/vending_machine.json';
    private const TEST_DB_PATH = 'tests/db/vending_machine.json';

    public function __construct()
    {
        $this->container = new Container();
        $this->container->parameter(InMemoryRepository::class, 'database', $this->getDbPath());
    }

    public function run(): void
    {
        /**
         * @var ActionHandler $actionHandler
         */
        $actionHandler = $this->container->get(ActionHandler::class);
        $actionHandler->handle();
    }

    private function getDbPath(): string
    {
        if (isset($_ENV['test'])) {
            return self::TEST_DB_PATH;
        }

        return self::DB_PATH;
    }
}

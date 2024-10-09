<?php

declare(strict_types=1);

namespace VendingMachine\Infrastructure;

use VendingMachine\Infrastructure\ActionHandler\GetActionHandler;
use VendingMachine\Infrastructure\ActionHandler\InsertActionHandler;
use VendingMachine\Infrastructure\ActionHandler\ReturnMoneyActionHandler;
use VendingMachine\Infrastructure\ActionHandler\ServiceActionHandler;

readonly class ActionHandler
{
    private const INSERT       = 'insert';
    private const RETURN_MONEY = 'return';
    private const GET          = 'get';
    private const SERVICE      = 'service';
    private const STOP         = 'exit';

    public function __construct(
        private InsertActionHandler $insertActionHandler,
        private ReturnMoneyActionHandler $returnMoneyActionHandler,
        private GetActionHandler $getActionHandler,
        private ServiceActionHandler $serviceActionHandler,
    ) {
    }

    public function handle(): void
    {
        while (true) {
            echo 'Choose an action (insert, return, get, service, exit): ';
            $handle = fopen('php://stdin', 'r');
            $action = trim(fgets($handle));
            switch ($action) {
                case self::INSERT:
                    $this->insertActionHandler->handle();
                    break;
                case self::RETURN_MONEY:
                    $this->returnMoneyActionHandler->handle();
                    break;
                case self::GET:
                    $this->getActionHandler->handle();
                    break;
                case self::SERVICE:
                    $this->serviceActionHandler->handle();
                    break;
                case self::STOP:
                    exit;
                default:
                    break;
            }
        }
    }
}

<?php

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Handler\InsertCoinCommandHandler;
use VendingMachine\Application\Service\ReturnCoins;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;
use VendingMachine\Infrastructure\System\Container;

require_once 'vendor/autoload.php';

const DB_PATH = "db/vending_machine.json";
const INSERT = "insert";
const RETURN_MONEY = "return";
const GET = "get";
const SERVICE = "service";
const STOP = "exit";

$container = new Container();
$container->parameter(InMemoryRepository::class,'database',DB_PATH);


while (true) {
    echo "Choose an action (insert, return, get, service, exit): ";
    $handle = fopen("php://stdin", "r");
    $action = trim(fgets($handle));
    switch ($action) {
        case INSERT:
            echo "Insert quantity: ";
            $handle = fopen("php://stdin", "r");
            $quantity = trim(fgets($handle));
            try {
                /**
                 * @var InsertCoinCommandHandler $handler
                 */
                $handler = $container->get(InsertCoinCommandHandler::class);
                $handler->handle(new InsertCoinCommand($quantity));
            } catch (Throwable $throwable) {
                echo $throwable->getMessage() . "\n";
            }

            break;
        case RETURN_MONEY:
            /**
             * @var ReturnCoins $handler
             */
            $handler = $container->get(ReturnCoins::class);
            $handler->handle();
            echo "Money returned\n";
            break;
        case GET:
            echo "get";
            break;
        case SERVICE:
            echo "service";
            break;
        case STOP:
            die;
        default:
            break;
    }
}

<?php

use VendingMachine\Application\Command\AddChangeCommand;
use VendingMachine\Application\Command\AddProductCommand;
use VendingMachine\Application\Command\GetProductCommand;
use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\SetPriceCommand;
use VendingMachine\Application\Command\SetProductQuantityCommand;
use VendingMachine\Application\Handler\AddChangeCommandHandler;
use VendingMachine\Application\Handler\AddProductCommandHandler;
use VendingMachine\Application\Handler\GetProductCommandHandler;
use VendingMachine\Application\Handler\InsertCoinCommandHandler;
use VendingMachine\Application\Handler\SetPriceCommandHandler;
use VendingMachine\Application\Handler\SetProductQuantityCommandHandler;
use VendingMachine\Application\Service\ListProducts;
use VendingMachine\Application\Service\ReturnCoins;
use VendingMachine\Infrastructure\Repository\InMemoryRepository;
use VendingMachine\Infrastructure\System\Container;

require_once 'vendor/autoload.php';

const DB_PATH = "db/vending_machine.json";
const TEST_DB_PATH = "tests/db/vending_machine.json";
const INSERT = "insert";
const RETURN_MONEY = "return";
const GET = "get";
const SERVICE = "service";
const STOP = "exit";

function getDbPath()
{
    if(isset($_ENV['test'])) {
        return TEST_DB_PATH;
    }

    return DB_PATH;
}

$dbPath = getDbPath();
$container = new Container();
$container->parameter(InMemoryRepository::class,'database', $dbPath);


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
                echo "Inserted\n";
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
            echo "Insert the quantity and the product name, example -> (10,SODA): ";
            $handle = fopen("php://stdin", "r");
            $getData = trim(fgets($handle));
            $getData = explode(',', $getData);
            while(count($getData) < 2) {
                echo "Provide a valid input\n";
                echo "Insert the quantity and the product name, example -> (10,SODA): ";
                $handle = fopen("php://stdin", "r");
                $getData = trim(fgets($handle));
                $getData = explode(',', $getData);
            }
            try {
                /**
                 * @var GetProductCommandHandler $getHandler
                 */
                $getHandler = $container->get(GetProductCommandHandler::class);
                $returnedAmount = $getHandler->handle(
                    new GetProductCommand($getData[0],$getData[1])
                );
                echo "Sold product: " . $getData[1] . "\n";
                echo "Returned money: " . $returnedAmount->value() . "\n";
            } catch (Throwable $exception) {
                echo $exception->getMessage() . "\n";
            }
            break;
        case SERVICE:
            echo "Choose an action (change, products, exit): ";
            $handle = fopen("php://stdin", "r");
            $serviceAction = trim(fgets($handle));
            while($serviceAction !== "exit" && $serviceAction !== "change" &&  $serviceAction !== "products") {
                echo "Choose an action (change, products, exit): ";
                $handle = fopen("php://stdin", "r");
                $serviceAction = trim(fgets($handle));
            }

            switch ($serviceAction){
                case "change":
                    echo "Insert the change: ";
                    $handle = fopen("php://stdin", "r");
                    $quantity = trim(fgets($handle));
                    /**
                     * @var AddChangeCommandHandler $handler
                     */
                    $handler = $container->get(AddChangeCommandHandler::class);
                    $handler->handle(new AddChangeCommand($quantity));
                    echo "Change added\n";
                    $serviceAction = "exit";
                    break;
                case "products":
                    echo "Choose an action (add, modify, cancel): ";
                    $handle = fopen("php://stdin", "r");
                    $productAction = trim(fgets($handle));
                    switch ($productAction) {
                        case "add":
                            echo "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): ";
                            $handle = fopen("php://stdin", "r");
                            $insertProductData = trim(fgets($handle));
                            $insertProductData = explode(',', $insertProductData);
                            while(count($insertProductData) < 3) {
                                echo "Provide a valid input\n";
                                echo "Insert the total elements, price and name following this format -> example: (10,0.45,Bread): ";
                                $handle = fopen("php://stdin", "r");
                                $insertProductData = trim(fgets($handle));
                                $insertProductData = explode(',', $insertProductData);
                            }
                            /**
                             * @var AddProductCommandHandler $handler
                             */
                            $handler = $container->get(AddProductCommandHandler::class);
                            $handler->handle(new AddProductCommand(
                                $insertProductData[2],
                                $insertProductData[0],
                                $insertProductData[1],
                            ));
                            echo "Product added\n";
                            break;
                        case "modify":
                            /**
                             * @var ListProducts $listProducts
                             */
                            $listProducts = $container->get(ListProducts::class);
                            $products = $listProducts->handle();
                            $availableIds = [];
                            echo "Select the product: \n";
                            foreach ($products as $product) {
                                echo "[" . $product['id'] ."] " . $product['name'] . "\n";
                                $availableIds[] = $product['id'];
                            }
                            echo "Enter the id: ";
                            $handle = fopen("php://stdin", "r");
                            $id = trim(fgets($handle));

                            while(!in_array($id, $availableIds)) {
                                echo "Enter the id: ";
                                $handle = fopen("php://stdin", "r");
                                $id = trim(fgets($handle));
                            }
                            echo "What do you want to modify? (quantity, price, cancel): ";
                            $handle = fopen("php://stdin", "r");
                            $modifyWhat = trim(fgets($handle));
                            while($modifyWhat !== "quantity" && $modifyWhat !== "price" && $modifyWhat !== "cancel") {
                                echo "What do you want to modify? (quantity, price, cancel): ";
                                $handle = fopen("php://stdin", "r");
                                $modifyWhat = trim(fgets($handle));
                            }

                            switch ($modifyWhat) {
                                case "quantity":
                                    echo "Enter the quantity: ";
                                    $handle = fopen("php://stdin", "r");
                                    $quantity = trim(fgets($handle));
                                    /**
                                     * @var SetProductQuantityCommandHandler $setProductQuantityHandler
                                     */
                                    $setProductQuantityHandler = $container->get(SetProductQuantityCommandHandler::class);
                                    $setProductQuantityHandler->handle(new SetProductQuantityCommand(
                                        $quantity,
                                        $id
                                    ));
                                    echo "Quantity modified\n";
                                    break;
                                case "price":
                                    echo "Enter the price: ";
                                    $handle = fopen("php://stdin", "r");
                                    $price = trim(fgets($handle));
                                    /**
                                     * @var SetPriceCommandHandler $setPriceHandler
                                     */
                                    $setPriceHandler = $container->get(SetPriceCommandHandler::class);
                                    $setPriceHandler->handle(new SetPriceCommand(
                                        $price,
                                        $id
                                    ));
                                    echo "Price modified\n";
                                    break;
                            }

                            break;
                        case "cancel":
                            $serviceAction = "exit";
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
            break;
        case STOP:
            die;
        default:
            break;
    }
}

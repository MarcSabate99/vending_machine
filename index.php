<?php

declare(strict_types=1);

use VendingMachine\Kernel;

require_once 'vendor/autoload.php';

$kernel = new Kernel();
$kernel->run();
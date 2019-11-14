<?php

require_once "autoload.php";

use src\classes\ParkingLot;
use src\classes\Transactions;
use src\classes\Prices;

use src\classes\Database;

Database::createDB();

// ParkingLot::listParkingLots();

$meuEstacionamento = new ParkingLot(50, 'myParking');
// $meuEstacionamento->deleteParkingLot();
$meuEstacionamento->registerNewPrice('hour', 28.99);
$meuEstacionamento->registerNewPrice('day', 1358.99);
// Prices::removePrice(2);

$meuEstacionamento->parkVehicle('HYB-0987', 'Car', 'GOL');
// $meuEstacionamento->listAllPrices();

$details = $meuEstacionamento->getTotalAmount('HYB-0987');

// $meuEstacionamento->listAllTransactions();
$receipt = $meuEstacionamento->confirmPayment($details);
// $Transaction = new Transactions();
// $Transaction->removeTransaction(5);

echo "TESTING SPOTS CREATION <br> <pre>";
print_r($receipt);
echo "<br>";

echo "</pre>";

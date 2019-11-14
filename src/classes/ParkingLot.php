<?php

namespace src\classes;

// Class that defines the parking lot space
class ParkingLot
{
    private $totalSpots;
    private $id;
    public $name;

    function __construct($numberOfSpots, $name)
    {
        $this->totalSpots = $numberOfSpots;
        $this->name = $name;

        //Try to find parking lot in the system
        $query_raw = "SELECT id FROM parking_lot WHERE park_name = ?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $this->name);

        $sql->execute();

        $results = $sql->get_result();

        if ($results->num_rows == 0) {

            $this->id = $this->createParkingLot();
        } else {
            $result = $results->fetch_all();

            $this->id = $result[0][0];
        }
    }

    function createParkingLot()
    {
        $query_raw = "INSERT INTO parking_lot (park_name, total_spots) VALUES (?, ?);";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('ss', $this->name,  $this->totalSpots);
        $sql->execute();

        return $sql->insert_id;
    }

    function deleteParkingLot()
    {
        $query_raw = "DELETE FROM parking_lot WHERE park_name=?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $this->name);
        return $sql->execute();
    }

    static function listParkingLots()
    {
        $sql = "SELECT park_name as name FROM parking_lot ORDER BY park_name ASC";
        $connection = Database::connectToDB();

        $results = $connection->query($sql)->fetch_all();

        return $results;
    }

    function registerNewPrice($type, $value)
    {
        $price = new Prices($value, $type);
        return $price->addPrice($this->id);
    }

    function listAllPrices()
    {
        $results = Prices::listPrices($this->id);
        return $results;
    }

    function parkVehicle($plate_number, $vehicle_type, $brand)
    {
        //Check if parking lot has space
        if (Spots::countSpots($this->id) < $this->totalSpots) {
            $spots = new Spots($plate_number, $vehicle_type, $brand);
            $spots->useSpot($this->id);
            return true;
        } else {
            return false;
        }
    }


    function getTotalAmount($plate_number)
    {
        $spots = new Spots($plate_number, '', '');
        if ($spots->loadSpot()) {
            $start_time = $spots->getTime();
            $end_time = date("Y-m-d H:i:s");
        }


        $totalTime = round((strtotime($end_time) - strtotime($start_time)) / (3600), 2);

        //If user was there for less than 10 hours, get hourly value, get daily value otherwise
        if ($totalTime < 10) {
            $price = Prices::getPrice($this->id, 'hour');
        } else {
            $totalTime = ceil($totalTime / 24); //Total Time rounded in days
            $price = Prices::getPrice($this->id, 'day');
        }

        $totalPrice = round($totalTime * $price, 2);

        return [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'total' => $totalPrice,
            'plate_number' => $plate_number,
        ];
    }

    function confirmPayment($paymentDetails)
    {

        //Add transaction details
        Transactions::addTransaction($this->id, $paymentDetails);

        //Free spot
        Spots::freeSpot($paymentDetails['plate_number']);

        //Return Receipt
        $receipt = Transactions::makeReceipt($paymentDetails);
        return $receipt;
    }

    function listAllTransactions()
    {
        return Transactions::listTransactions($this->id);
    }
}

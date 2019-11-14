<?php

namespace src\classes;

class Prices
{
    private $value;
    private $type;

    function __construct($value, $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    function addPrice($parking_id)
    {
        $query_raw = "INSERT INTO prices (type_pricing, value_price, id_parking_lot) VALUES (?, ?, ?);";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('sss', $this->type,  $this->value, $parking_id);
        return $sql->execute();
    }

    static function removePrice($price_id)
    {
        $query_raw = "DELETE FROM prices WHERE id=?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $price_id);
        return $sql->execute();
    }

    static function listPrices($parking_id)
    {
        $query_raw = "SELECT id, value_price, type_pricing FROM prices WHERE id_parking_lot = ? ORDER BY id DESC";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('i', $parking_id);
        $sql->execute();
        $results = $sql->get_result()->fetch_all();

        return $results;
    }

    function getPrice($park_id, $type)
    {
        $query_raw = "SELECT value_price FROM prices WHERE id_parking_lot = ? AND type_pricing = ? ORDER BY id DESC";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('is', $park_id, $type);
        $sql->execute();
        $results = $sql->get_result();

        if ($results->num_rows > 0) {
            $results = $results->fetch_all();

            return $results[0][0];
        } else {
            return 0;
        }
    }

    // function printReceipt($carDetails, $totalTime)
    // {
    //     return "O VEICULO " . $carDetails['registry'] . " PAGOU " . $this->getAmount($totalTime) . "PELO TEMPO " . $totalTime;
    // }
}

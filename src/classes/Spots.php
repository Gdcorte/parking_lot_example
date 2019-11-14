<?php

namespace src\classes;

// Class that defines the state of a spot
class Spots
{
    private $id;
    private $plate_number;
    private $start_time;
    private $vehicle_type;
    private $brand;

    function __construct($plate_number, $vehicle_type, $brand)
    {

        $this->plate_number = $plate_number;
        $this->start_time = date("Y-m-d H:i:s");
        $this->vehicle_type = $vehicle_type;
        $this->brand = $brand;
    }

    function useSpot($parking_id)
    {
        $query_raw = "INSERT INTO spots (plate_number, start_time, brand, vehicle_type, id_parking_lot) VALUES (?, ?, ?, ?, ?);";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('sssss', $this->plate_number,  $this->start_time, $this->brand, $this->vehicle_type, $parking_id);
        $sql->execute();

        $this->id = $sql->insert_id;
        return true;
    }

    static function countSpots($parking_id)
    {
        $query_raw = "SELECT count(id) FROM spots WHERE id_parking_lot = ? GROUP BY id_parking_lot";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('i', $parking_id);
        $sql->execute();
        $results = $sql->get_result();

        if ($results->num_rows == 0) {
            $results = 0;
        } else {
            $results = $results->fetch_all();
            $results = $results[0][0];
        }

        return $results;
    }

    function loadSpot()
    {
        $query_raw = "SELECT id, start_time, vehicle_type,brand FROM spots WHERE plate_number=?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $this->plate_number);
        $sql->execute();

        $results = $sql->get_result();



        if ($results->num_rows == 0) {
            return false;
        } else {
            $results = $results->fetch_all();
            $this->id = $results[0][0];
            $this->start_time = $results[0][1];;
            $this->vehicle_type = $results[0][2];;
            $this->brand = $results[0][3];
            return true;
        }
    }

    function getTime()
    {
        return $this->start_time;
    }

    static function freeSpot($plate_number)
    {
        $query_raw = "DELETE FROM spots WHERE plate_number=?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $plate_number);
        return $sql->execute();
    }

    static function listSpots($parking_id)
    {
        $query_raw = "SELECT id, value_price, type_pricing FROM prices WHERE id_parking_lot = ? ORDER BY id DESC";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('i', $parking_id);
        $sql->execute();
        $results = $sql->get_result()->fetch_all();

        return $results;
        echo "AQUI <pre>";
        print_r($results);
        die;
    }
}

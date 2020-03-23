<?php

namespace src\classes;

use mysqli;
use PHPUnit\Framework\Error\Error;

class Database
{
    const DB_HOSTNAME = "localhost";
    const DB_USERNAME = "Gustavo";
    const DB_PASSWORD = "********";
    const DB_DATABASE = 'parking_lot';

    private $conn;

    static function connectToDB()
    {
        try {
            // Create connection
            $connection = new mysqli(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_DATABASE);
            return $connection;
        } catch (Error $e) {
            return "FAILED TO CONNECT:" . $e;
        }
    }

    static function createDB()
    {
        try {
            //try to connect to server
            $connection = $connection = new mysqli(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD);

            $sql = "CREATE DATABASE " . self::DB_DATABASE;
            $status = $connection->query($sql);
            $connection->close();

            if (!$status) {
                return 'ERROR CREATING DATABASE';
            } else {
                self::createBasicStructure();
                return 'SUCCESS';
            }
        } catch (Error $e) {
            return "FAILED TO CONNECT TO SERVER: " . $e;
        }
    }

    static function createBasicStructure()
    {
        $sql = 'CREATE TABLE parking_lot(
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                park_name VARCHAR(30) NOT NULL UNIQUE,
                total_spots INT NOT NULL
            );
            CREATE TABLE transactions(
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_parking_lot INT UNSIGNED,
                plate_number VARCHAR(30) NOT NULL UNIQUE,
                start_time DATETIME NOT NULL ,
                end_time DATETIME NOT NULL,
                total FLOAT,
                CONSTRAINT fk_transactions_parking_lot FOREIGN KEY (id_parking_lot) REFERENCES parking_lot(id)
            );

            CREATE TABLE spots(
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_parking_lot INT UNSIGNED,
                plate_number VARCHAR(30) NOT NULL,
                start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                vehicle_type VARCHAR(30) NOT NULL,
                brand VARCHAR(30) NOT NULL,
                CONSTRAINT fk_spots_parking_lot FOREIGN KEY (id_parking_lot) REFERENCES parking_lot(id)
            );

            CREATE TABLE prices(
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                id_parking_lot INT UNSIGNED,
                type_pricing ENUM("day", "hour", "month") DEFAULT "hour",
                value_price FLOAT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_prices_parking_lot FOREIGN KEY (id_parking_lot) REFERENCES parking_lot(id)
            );';

        return self::makeMultiQuery($sql);
    }

    static function makeSingleQuery($sql)
    {
        $connection = self::connectToDB();
        $status = $connection->query($sql);
        $connection->close();

        if (!$status) {
            return "ERROR MAKING QUERY";
        } else {

            return "SUCCESS";
        }
    }

    static function makeMultiQuery($sql)
    {
        $connection = self::connectToDB();
        $status = $connection->multi_query($sql);
        $connection->close();

        if (!$status) {
            return "ERROR MAKING QUERY";
        } else {
            return "SUCCESS";
        }
    }

    static function closeConnection($connection)
    {
        $connection->close();
    }
}

<?php

namespace src\classes;

// Class that defines the state of a spot
class Transactions
{
    static function addTransaction($parking_id, $transactionDetails)
    {
        $query_raw = "INSERT INTO transactions (plate_number, start_time, end_time, total, id_parking_lot) VALUES (?, ?, ?, ?, ?);";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param(
            'sssdi',
            $transactionDetails['plate_number'],
            $transactionDetails['start_time'],
            $transactionDetails['end_time'],
            $transactionDetails['total'],
            $parking_id
        );
        return $sql->execute();
    }

    static function removeTransaction($transaction_id)
    {
        $query_raw = "DELETE FROM transactions WHERE id=?";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('s', $transaction_id);
        return $sql->execute();
    }

    static function listTransactions($parking_id)
    {
        $query_raw = "SELECT id, plate_number, start_time, end_time, total FROM transactions WHERE id_parking_lot = ? ORDER BY id DESC";
        $connection = Database::connectToDB();

        $sql = $connection->prepare($query_raw);
        $sql->bind_param('i', $parking_id);
        $sql->execute();
        $results = $sql->get_result()->fetch_all();

        return $results;
    }

    static function makeReceipt($transactionDetails)
    {
        $receipt = "THE USER WITH LICENSE PLATE " . $transactionDetails['plate_number'] . " HAS PAID " . $transactionDetails['total'] .
            " AND STAYED FROM " . $transactionDetails['start_time'] . " TO " . $transactionDetails['end_time'] . ".";
        return $receipt;
    }
}

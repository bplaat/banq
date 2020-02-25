<?php

// A wrapper class around PDO
class Database {
    protected static $pdo, $queryCount;

    // A function that connects to a database
    public static function connect ($dsn, $user, $password) {
        static::$pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        static::$queryCount = 0;
    }

    // A function the returns the query count
    public static function queryCount () {
        return static::$queryCount;
    }

    // A function which returns the last insert id
    public static function lastInsertId () {
        return static::$pdo->lastInsertId();
    }

    // A function that returns a prepared statement
    public static function query ($query, ...$parameters) {
        static::$queryCount++;
        $statement = static::$pdo->prepare($query);
        $statement->execute($parameters);
        return $statement;
    }
}

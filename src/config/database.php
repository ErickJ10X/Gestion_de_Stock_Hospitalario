<?php

namespace config;

use PDO;
use PDOException;

class Database
{
    private static $host = 'localhost';
    private static $db_name = 'gestion_stock_hospitalario';
    private static $username = 'root';
    private static $password = '';
    private static $conn;

    public static function connect()
    {
        self::$conn = null;

        try {
            self::$conn = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$db_name . ';charset=utf8', 
                self::$username, self::$password);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
            die();
        }

        return self::$conn;
    }
}

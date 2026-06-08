<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/config.php';

            $host = $config['database']['host'];
            $port = $config['database']['port'];
            $database = $config['database']['name'];
            $user = $config['database']['user'];
            $password = $config['database']['password'];

            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

            try {
                self::$connection = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                die('Błąd połączenia z bazą danych.');
            }
        }

        return self::$connection;
    }
}
<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * PDO singleton — one connection per request.
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    /**
     * Return the shared PDO connection, creating it on first call.
     *
     * @return PDO  The active database connection.
     *
     * @throws RuntimeException  If the connection cannot be established.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $dbConfig = require ROOT_PATH . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $dbConfig['host'],
            $dbConfig['dbname'],
            $dbConfig['charset']
        );

        try {
            self::$instance = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $pdoException) {
            throw new RuntimeException(
                'Database connection failed: ' . $pdoException->getMessage(),
                (int) $pdoException->getCode(),
                $pdoException
            );
        }

        return self::$instance;
    }
}

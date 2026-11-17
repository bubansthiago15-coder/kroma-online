<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;
    private static ?string $driverName = null;

    public static function driver(): string
    {
        if (self::$driverName === null) {
            $config = require ROOT . '/config/database.php';
            self::$driverName = $config['driver'];
        }
        return self::$driverName;
    }

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $config = require ROOT . '/config/database.php';
            self::$driverName = $config['driver'];

            if (!in_array(self::$driverName, PDO::getAvailableDrivers(), true)) {
                throw new RuntimeException('El servidor PHP no tiene habilitado PDO para ' . self::$driverName . '.');
            }

            if (self::$driverName === 'pgsql') {
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
                    $config['host'],
                    $config['port'],
                    $config['name'],
                    $config['sslmode']
                );
            } else {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'],
                    $config['port'],
                    $config['name'],
                    $config['charset']
                );
            }

            self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    public static function insertReturning(string $sql, string $column): string
    {
        return self::driver() === 'pgsql' ? $sql . ' RETURNING ' . $column : $sql;
    }

    public static function insertedId(PDO $db, PDOStatement $statement): int
    {
        return self::driver() === 'pgsql'
            ? (int) $statement->fetchColumn()
            : (int) $db->lastInsertId();
    }
}

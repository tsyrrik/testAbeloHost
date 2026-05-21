<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    public static function create(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: 'mysql',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_NAME') ?: 'blog',
        );

        return new PDO(
            $dsn,
            getenv('DB_USER') ?: 'blog',
            getenv('DB_PASS') ?: 'blog',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\Integration;

use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

abstract class IntegrationTestCase extends TestCase
{
    protected static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = self::connect();
        self::resetSchema();
        self::loadFixtures();
    }

    private static function connect(): PDO
    {
        $dbName = getenv('DB_NAME') ?: 'blog_test';

        if (!str_ends_with($dbName, '_test')) {
            throw new RuntimeException(sprintf(
                'Integration tests target database "%s", which is not a test database. '
                . 'Expected name ending with "_test". Check phpunit.xml env overrides.',
                $dbName,
            ));
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: 'mysql',
            getenv('DB_PORT') ?: '3306',
            $dbName,
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

    private static function resetSchema(): void
    {
        self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        self::$pdo->exec('DROP TABLE IF EXISTS article_categories');
        self::$pdo->exec('DROP TABLE IF EXISTS articles');
        self::$pdo->exec('DROP TABLE IF EXISTS categories');
        self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $dir = dirname(__DIR__, 2) . '/src/Database/Migrations';
        foreach (glob($dir . '/*.sql') ?: [] as $path) {
            self::$pdo->exec((string) file_get_contents($path));
        }
    }

    private static function loadFixtures(): void
    {
        $categories = [
            1 => ['tech',   'Tech',   'Tech category'],
            2 => ['travel', 'Travel', 'Travel category'],
            3 => ['food',   'Food',   'Food category'],
        ];
        foreach ($categories as $id => [$slug, $title, $desc]) {
            self::$pdo->prepare(
                'INSERT INTO categories (id, slug, title, description) VALUES (?, ?, ?, ?)'
            )->execute([$id, $slug, $title, $desc]);
        }

        $articles = [
            [1, 'tech-a',        'Tech A',         100, '2026-01-01 12:00:00', [1]],
            [2, 'tech-b',        'Tech B',         500, '2026-02-01 12:00:00', [1]],
            [3, 'tech-travel',   'Tech Travel',    200, '2026-03-01 12:00:00', [1, 2]],
            [4, 'travel-a',      'Travel A',        50, '2026-04-01 12:00:00', [2]],
            [5, 'food-a',        'Food A',        1000, '2026-05-01 12:00:00', [3]],
            [6, 'tech-travel-2', 'Tech Travel 2',  300, '2026-05-15 12:00:00', [1, 2]],
        ];

        $insertA = self::$pdo->prepare(
            'INSERT INTO articles (id, slug, title, description, body, image_path, views, published_at)
             VALUES (?, ?, ?, ?, ?, NULL, ?, ?)'
        );
        $insertL = self::$pdo->prepare(
            'INSERT INTO article_categories (article_id, category_id) VALUES (?, ?)'
        );

        foreach ($articles as [$id, $slug, $title, $views, $date, $catIds]) {
            $insertA->execute([$id, $slug, $title, $title . ' description', '<p>body</p>', $views, $date]);
            foreach ($catIds as $cid) {
                $insertL->execute([$id, $cid]);
            }
        }
    }
}

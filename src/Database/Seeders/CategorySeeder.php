<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use PDO;

final class CategorySeeder
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array<string, int> slug => id */
    public function run(): array
    {
        $data = require __DIR__ . '/data/categories.php';

        $sql = 'INSERT INTO categories (slug, title, description)
                VALUES (:slug, :title, :description)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    description = VALUES(description)';
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $row) {
            $stmt->execute([
                'slug' => $row['slug'],
                'title' => $row['title'],
                'description' => $row['description'],
            ]);
        }

        $map = [];
        foreach ($this->pdo->query('SELECT id, slug FROM categories')->fetchAll() as $row) {
            $map[$row['slug']] = (int) $row['id'];
        }

        return $map;
    }
}

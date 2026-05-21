<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Categories that have at least one article, ordered by title.
     *
     * @return array<int, array{id:int, slug:string, title:string, description:?string}>
     */
    public function findAllWithArticles(): array
    {
        $sql = <<<SQL
            SELECT c.id, c.slug, c.title, c.description
            FROM categories c
            WHERE EXISTS (
                SELECT 1
                FROM article_categories ac
                WHERE ac.category_id = c.id
            )
            ORDER BY c.title
        SQL;

        $rows = $this->pdo->query($sql)->fetchAll();

        return array_map(self::hydrate(...), $rows);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, slug, title, description FROM categories WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row === false ? null : self::hydrate($row);
    }

    /**
     * @param array{id:string|int, slug:string, title:string, description:?string} $row
     * @return array{id:int, slug:string, title:string, description:?string}
     */
    private static function hydrate(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'slug' => $row['slug'],
            'title' => $row['title'],
            'description' => $row['description'],
        ];
    }
}

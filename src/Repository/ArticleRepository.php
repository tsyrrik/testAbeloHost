<?php
declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class ArticleRepository
{
    private const SORTS = [
        'date' => 'a.published_at',
        'views' => 'a.views',
    ];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array<int, list<array<string, mixed>>> category_id => articles */
    public function latestGroupedByCategories(int $perCategory = 3): array
    {
        $sql = <<<SQL
            SELECT *
            FROM (
                SELECT a.id, a.slug, a.title, a.description, a.image_path,
                       a.views, a.published_at, ac.category_id,
                       ROW_NUMBER() OVER (
                           PARTITION BY ac.category_id
                           ORDER BY a.published_at DESC, a.id DESC
                       ) AS rn
                FROM articles a
                JOIN article_categories ac ON ac.article_id = a.id
            ) ranked
            WHERE rn <= :limit
            ORDER BY category_id, published_at DESC, id DESC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('limit', $perCategory, PDO::PARAM_INT);
        $stmt->execute();

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $categoryId = (int) $row['category_id'];
            unset($row['category_id'], $row['rn']);
            $grouped[$categoryId][] = self::hydrate($row);
        }

        return $grouped;
    }

    /** @return array{items: list<array<string, mixed>>, total: int, page: int} */
    public function paginateByCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        if (!isset(self::SORTS[$sort])) {
            throw new InvalidArgumentException("Unknown sort: {$sort}");
        }
        $orderColumn = self::SORTS[$sort];

        $countStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM article_categories WHERE category_id = :cat'
        );
        $countStmt->execute(['cat' => $categoryId]);
        $total = (int) $countStmt->fetchColumn();

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $sql = <<<SQL
            SELECT a.id, a.slug, a.title, a.description, a.image_path,
                   a.views, a.published_at
            FROM articles a
            JOIN article_categories ac ON ac.article_id = a.id
            WHERE ac.category_id = :cat
            ORDER BY {$orderColumn} DESC, a.id DESC
            LIMIT :limit OFFSET :offset
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => array_map(self::hydrate(...), $stmt->fetchAll()),
            'total' => $total,
            'page' => $page,
        ];
    }

    public function findDetailedBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, slug, title, description, body, image_path, views, published_at
             FROM articles WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        $article = self::hydrate($row);
        $article['body'] = $row['body'];
        $article['categories'] = $this->findCategoriesForArticle($article['id']);

        return $article;
    }

    /** @return list<array{id:int, slug:string, title:string}> */
    public function findCategoriesForArticle(int $articleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.slug, c.title
             FROM categories c
             JOIN article_categories ac ON ac.category_id = c.id
             WHERE ac.article_id = :id
             ORDER BY c.title'
        );
        $stmt->execute(['id' => $articleId]);

        $categories = [];
        foreach ($stmt->fetchAll() as $row) {
            $categories[] = [
                'id' => (int) $row['id'],
                'slug' => $row['slug'],
                'title' => $row['title'],
            ];
        }

        return $categories;
    }

    /**
     * @param list<int> $categoryIds
     * @return list<array<string, mixed>>
     */
    public function findRelated(int $articleId, array $categoryIds, int $limit = 3): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $sql = <<<SQL
            SELECT a.id, a.slug, a.title, a.description, a.image_path,
                   a.views, a.published_at,
                   COUNT(ac.category_id) AS match_count
            FROM articles a
            JOIN article_categories ac ON ac.article_id = a.id
            WHERE ac.category_id IN ({$placeholders})
              AND a.id <> ?
            GROUP BY a.id
            ORDER BY match_count DESC, a.published_at DESC, a.id DESC
            LIMIT ?
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $i = 1;
        foreach ($categoryIds as $id) {
            $stmt->bindValue($i++, $id, PDO::PARAM_INT);
        }
        $stmt->bindValue($i++, $articleId, PDO::PARAM_INT);
        $stmt->bindValue($i, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(self::hydrate(...), $stmt->fetchAll());
    }

    public function incrementViews(int $articleId): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $articleId]);
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private static function hydrate(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'slug' => $row['slug'],
            'title' => $row['title'],
            'description' => $row['description'] ?? null,
            'image_path' => $row['image_path'] ?? null,
            'views' => (int) $row['views'],
            'published_at' => $row['published_at'],
        ];
    }
}

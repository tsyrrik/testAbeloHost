<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use PDO;
use RuntimeException;

final class ArticleSeeder
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @param array<string, int> $categoryMap slug => id */
    public function run(array $categoryMap): int
    {
        $data = require __DIR__ . '/data/articles.php';

        $insertArticle = $this->pdo->prepare(
            'INSERT INTO articles
                (slug, title, description, body, image_path, views, published_at)
             VALUES
                (:slug, :title, :description, :body, :image_path, :views, :published_at)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                body = VALUES(body),
                image_path = VALUES(image_path),
                published_at = VALUES(published_at)'
        );
        $findId = $this->pdo->prepare('SELECT id FROM articles WHERE slug = :slug');
        $deleteLinks = $this->pdo->prepare(
            'DELETE FROM article_categories WHERE article_id = :id'
        );
        $insertLink = $this->pdo->prepare(
            'INSERT INTO article_categories (article_id, category_id)
             VALUES (:article_id, :category_id)'
        );

        $count = 0;
        foreach ($data as $row) {
            $insertArticle->execute([
                'slug' => $row['slug'],
                'title' => $row['title'],
                'description' => $row['description'],
                'body' => $row['body'],
                'image_path' => $row['image_path'],
                'views' => $row['views'],
                'published_at' => $row['published_at'],
            ]);

            $findId->execute(['slug' => $row['slug']]);
            $articleId = (int) $findId->fetchColumn();

            $deleteLinks->execute(['id' => $articleId]);
            foreach ($row['categories'] as $catSlug) {
                if (!isset($categoryMap[$catSlug])) {
                    throw new RuntimeException(sprintf(
                        'Article "%s" references unknown category slug "%s"',
                        $row['slug'],
                        $catSlug,
                    ));
                }
                $insertLink->execute([
                    'article_id' => $articleId,
                    'category_id' => $categoryMap[$catSlug],
                ]);
            }
            $count++;
        }

        return $count;
    }
}

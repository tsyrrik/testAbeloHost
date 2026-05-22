<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Support\HtmlSanitizer;

final class ArticleController
{
    private const RELATED_LIMIT = 3;

    public function __construct(
        private readonly View $view,
        private readonly ArticleRepository $articles,
    ) {
    }

    public function show(array $params): void
    {
        $article = $this->articles->findDetailedBySlug($params['slug']);
        if ($article === null) {
            throw new NotFoundException('Article not found: ' . $params['slug']);
        }

        $this->articles->incrementViews($article['id']);
        $article['views']++;
        $article['body'] = HtmlSanitizer::articleBody($article['body']);

        $categoryIds = array_map(static fn (array $c): int => $c['id'], $article['categories']);
        $related = $this->articles->findRelated($article['id'], $categoryIds, self::RELATED_LIMIT);

        $this->view->display('article', [
            'title' => $article['title'],
            'article' => $article,
            'related' => $related,
        ]);
    }
}

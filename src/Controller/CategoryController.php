<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Support\Paginator;

final class CategoryController
{
    private const PER_PAGE = 6;
    private const ALLOWED_SORTS = ['date', 'views'];
    private const DEFAULT_SORT = 'date';

    public function __construct(
        private readonly View $view,
        private readonly CategoryRepository $categories,
        private readonly ArticleRepository $articles,
    ) {
    }

    public function show(array $params): void
    {
        $category = $this->categories->findBySlug($params['slug']);
        if ($category === null) {
            throw new NotFoundException('Category not found: ' . $params['slug']);
        }

        $sort = Request::query('sort', self::DEFAULT_SORT);
        if (!in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = self::DEFAULT_SORT;
        }
        $page = max(1, Request::queryInt('page', 1));

        $result = $this->articles->paginateByCategory(
            $category['id'],
            $sort,
            $page,
            self::PER_PAGE,
        );

        $paginator = new Paginator($result['total'], self::PER_PAGE, $page);

        $this->view->display('category', [
            'title' => $category['title'],
            'category' => $category,
            'articles' => $result['items'],
            'sort' => $sort,
            'pagination' => $paginator->toArray(),
        ]);
    }
}

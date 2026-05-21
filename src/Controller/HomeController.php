<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

final class HomeController
{
    public function __construct(
        private readonly View $view,
        private readonly CategoryRepository $categories,
        private readonly ArticleRepository $articles,
    ) {
    }

    public function index(): void
    {
        $categories = $this->categories->findAllWithArticles();
        $latestByCategory = $this->articles->latestGroupedByCategories(3);

        $sections = [];
        foreach ($categories as $category) {
            $sections[] = [
                'category' => $category,
                'articles' => $latestByCategory[$category['id']] ?? [],
            ];
        }

        $this->view->display('home', [
            'title' => 'Blog',
            'sections' => $sections,
        ]);
    }
}

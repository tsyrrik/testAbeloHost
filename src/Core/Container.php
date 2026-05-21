<?php
declare(strict_types=1);

namespace App\Core;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use PDO;
use Smarty\Smarty;

final class Container
{
    private ?PDO $pdo = null;
    private ?View $view = null;
    private ?CategoryRepository $categoryRepository = null;
    private ?ArticleRepository $articleRepository = null;

    public function __construct(private readonly string $basePath)
    {
    }

    public function pdo(): PDO
    {
        return $this->pdo ??= Database::create();
    }

    public function view(): View
    {
        if ($this->view !== null) {
            return $this->view;
        }

        $smarty = new Smarty();
        $smarty->setTemplateDir($this->basePath . '/templates');
        $smarty->setCompileDir($this->basePath . '/templates_c');
        $smarty->setCacheDir($this->basePath . '/cache');
        $smarty->setEscapeHtml(true);

        return $this->view = new View($smarty);
    }

    public function categoryRepository(): CategoryRepository
    {
        return $this->categoryRepository ??= new CategoryRepository($this->pdo());
    }

    public function articleRepository(): ArticleRepository
    {
        return $this->articleRepository ??= new ArticleRepository($this->pdo());
    }
}

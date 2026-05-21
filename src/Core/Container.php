<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use Smarty\Smarty;

final class Container
{
    private ?PDO $pdo = null;
    private ?View $view = null;

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
}

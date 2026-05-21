<?php
declare(strict_types=1);

namespace App\Core;

use Smarty\Smarty;

final class View
{
    public function __construct(private readonly Smarty $smarty)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $this->smarty->assign($data);

        return $this->smarty->fetch($template . '.tpl');
    }

    public function display(string $template, array $data = []): void
    {
        $this->smarty->assign($data);
        $this->smarty->display($template . '.tpl');
    }
}

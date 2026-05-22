<?php
declare(strict_types=1);

namespace App\Support;

final class Paginator
{
    public readonly int $pages;
    public readonly int $page;

    public function __construct(
        public readonly int $total,
        public readonly int $perPage,
        int $page,
    ) {
        $this->pages = max(1, (int) ceil($total / $perPage));
        $this->page = max(1, min($page, $this->pages));
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'per_page' => $this->perPage,
            'page' => $this->page,
            'pages' => $this->pages,
            'has_prev' => $this->page > 1,
            'has_next' => $this->page < $this->pages,
            'range' => range(1, $this->pages),
        ];
    }
}

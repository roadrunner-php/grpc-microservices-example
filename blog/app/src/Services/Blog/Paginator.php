<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Spiral\Pagination\PaginatorInterface;

final class Paginator
{
    private int $perPage;

    public function __construct(
        private PaginatorInterface $paginator,
        private array $items,
        private int $currentPage = 1
    ) {
        $this->perPage = $paginator->getLimit();
        $this->currentPage = $paginator->getPage();
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->paginator->count();
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function lastPage(): int
    {
        return $this->paginator->countPages();
    }

    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'total' => $this->total(),
        ];
    }
}

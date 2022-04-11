<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Spiral\RoadRunner\GRPC\Exception\NotFoundException;

final class PostRepository extends Repository
{
    public function paginate(int $page = 1, int $perPage = 25): Paginator
    {
        return $this->paginateQuery($this->select(), $page, $perPage);
    }

    protected function paginateQuery(Select $query, int $page = 1, int $perPage = 20): Paginator
    {
        $paginator = (new \Spiral\Pagination\Paginator($perPage))->withPage($page)->paginate($query);

        return new Paginator(
            $paginator,
            $query->fetchAll(),
            $page
        );
    }

    /**
     * @throws \Exception
     */
    public function getByPK(int $id): Post
    {
        return $this->findByPK($id) ?? throw new NotFoundException('Post not found');
    }
}

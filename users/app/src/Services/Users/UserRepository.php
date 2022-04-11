<?php

declare(strict_types=1);

namespace App\Services\Users;

use Cycle\Database\Injection\Parameter;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Spiral\RoadRunner\GRPC\Exception\NotFoundException;

final class UserRepository extends Repository
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

    public function findAllInPKs(array $pks): array
    {
        return $this->select()->where('id', 'IN', new Parameter($pks))->fetchAll();
    }

    /**
     * @throws \Exception
     */
    public function getByPK(int $id): User
    {
        return $this->findByPK($id) ?? throw new NotFoundException('User not found');
    }

    /**
     * @throws \Exception
     */
    public function getByUsername(string $username): User
    {
        return $this->findOne(['username' => $username]) ?? throw new NotFoundException('User not found');
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Users;

use Google\Protobuf\Timestamp;
use Spiral\Shared\Services\Users\v1\DTO\User as UserDTO;

class UserDTOFactory
{
    public static function fromEntity(User $user): UserDTO
    {
        $dto = new UserDTO();
        $dto->setId($user->getId());
        $dto->setUsername($user->getUsername());
        $dto->setEmail($user->getEmail());
        $dto->setCreatedAt(new Timestamp(['seconds' => $user->getCreatedAt()->getTimestamp()]));
        $dto->setIsAdmin($user->isAdmin());

        return $dto;
    }
}

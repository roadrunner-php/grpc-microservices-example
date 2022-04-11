<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Google\Protobuf\Timestamp;
use Spiral\Shared\Services\Blog\v1\DTO\Post as PostDTO;
use Spiral\Shared\Services\Users\v1\DTO\User as UserDTO;

class PostDTOFactory
{
    public static function fromEntity(Post $post, ?UserDTO $user): PostDTO
    {
        $dto = new PostDTO();
        $dto->setId($post->getId());
        $dto->setTitle($post->getTitle());
        $dto->setText($post->getText());
        $dto->setCreatedAt(new Timestamp(['seconds' => $post->getCreatedAt()->getTimestamp()]));
        $dto->setAuthor($user);

        return $dto;
    }
}

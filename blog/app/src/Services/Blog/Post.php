<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;

#[Entity(
    repository: PostRepository::class,
    table: "posts",
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
)]
class Post
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct(
        #[Column(type: 'string')]
        private string $title,
        #[Column(type: 'string')]
        private string $text,
        #[Column(type: 'integer')]
        private int $authorId
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Users;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior;

#[Entity(
    repository: UserRepository::class,
    table: "users",
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
)]
class User
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'boolean', typecast: 'bool')]
    private bool $isAdmin = false;

    #[Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[Column(type: 'string')]
    private string $password;

    public function __construct(
        #[Column(type: 'string')]
        private string $username,
        #[Column(type: 'string')]
        private string $email,
        string $password,
    ) {
        $this->setPassword($password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function grantAdminPrivileges(): void
    {
        $this->isAdmin = true;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}

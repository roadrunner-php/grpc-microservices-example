<?php

declare(strict_types=1);

namespace Database\Seeder;

use App\Services\Users\User;
use Spiral\DatabaseSeeder\Factory\AbstractFactory;

final class UserFactory extends AbstractFactory
{
    public function entity(): string
    {
        return User::class;
    }

    public function definition(): array
    {
        return [
            'username' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'password' => password_hash('secret', PASSWORD_BCRYPT),
            'isAdmin' => $this->faker->boolean(),
            'createdAt' => $this->faker->dateTimeBetween('-30 days'),
        ];
    }
}

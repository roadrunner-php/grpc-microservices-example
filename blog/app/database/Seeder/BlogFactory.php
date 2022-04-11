<?php

declare(strict_types=1);

namespace Database\Seeder;

use App\Services\Blog\Post;
use Spiral\DatabaseSeeder\Factory\AbstractFactory;

final class BlogFactory extends AbstractFactory
{
    public function entity(): string
    {
        return Post::class;
    }

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'text' => $this->faker->text(),
            'authorId' => $this->faker->numberBetween(1, 100),
            'createdAt' => $this->faker->dateTimeBetween('-30 days'),
        ];
    }
}

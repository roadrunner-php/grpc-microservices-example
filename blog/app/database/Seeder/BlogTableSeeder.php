<?php

declare(strict_types=1);

namespace Database\Seeder;

use Spiral\DatabaseSeeder\Seeder\AbstractSeeder;

class BlogTableSeeder extends AbstractSeeder
{
    public function run(): \Generator
    {
        foreach (BlogFactory::new()->times(100)->create() as $post) {
            yield $post;
        }
    }
}

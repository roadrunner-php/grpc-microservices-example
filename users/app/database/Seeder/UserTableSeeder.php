<?php

declare(strict_types=1);

namespace Database\Seeder;

use Spiral\DatabaseSeeder\Seeder\AbstractSeeder;

class UserTableSeeder extends AbstractSeeder
{
    public function run(): \Generator
    {
        foreach (UserFactory::new()->times(100)->create() as $user) {
            yield $user;
        }
    }
}

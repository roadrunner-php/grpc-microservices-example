<?php

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefault2b85ad048812ab2358da809fcb73a343 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('posts')
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
            ])
            ->addColumn('id', 'primary', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('title', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('text', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('author_id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["id"])
            ->create();
    }

    public function down(): void
    {
        $this->table('posts')->drop();
    }
}

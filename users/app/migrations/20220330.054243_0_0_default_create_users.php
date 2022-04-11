<?php

namespace Migration;

use Cycle\Migrations\Migration;

class OrmDefaultEf1bdaf38b1a80b3a4ce08a7f641bcbc extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('users')
            ->addColumn('created_at', 'datetime', [
                'nullable' => false,
            ])
            ->addColumn('id', 'primary', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('is_admin', 'boolean', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('username', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('email', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('password', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["id"])
            ->create();
    }

    public function down(): void
    {
        $this->table('users')->drop();
    }
}

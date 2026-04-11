<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand the role enum to include 'counselor'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','recommender','counselor','admin') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        // Revert any counselors back to user before shrinking the enum
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'counselor'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','recommender','admin') NOT NULL DEFAULT 'user'");
    }
};

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@yassiru.com'],
            [
                'name' => 'مدير المنصة',
                'phone' => '+201000000000',
                'password' => Hash::make('admin123'),
                'gender' => 'male',
                'role' => 'admin',
                'email_verified_at' => now(),
                'has_certificate' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}

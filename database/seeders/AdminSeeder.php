<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * The administrator account, with a password this file does not know.
     *
     * It used to hardcode one. That is survivable while a repository is
     * private and fatal the moment it is not: the credentials of every
     * deployment that ran the seeder unchanged become public along with the
     * code. So the password is read from the environment and there is no
     * fallback — a seeder that invents a password quietly is how a weak one
     * reaches production.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (!is_string($password) || $password === '') {
            throw new RuntimeException(
                'ADMIN_PASSWORD is not set. Put one in .env before seeding — '
                . 'this seeder will not choose an administrator password for you.'
            );
        }

        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@yassiru.com')],
            [
                'name' => 'مدير المنصة',
                'phone' => '+201000000000',
                'password' => Hash::make($password),
                'gender' => 'male',
                'role' => 'admin',
                'email_verified_at' => now(),
                // Admins don't need certificates — they're oversight, not students.
                'has_certificate' => false,
            ]
        );

        $admin->assignRole('admin');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Admin
            'manage-users',
            'manage-recommenders',
            'manage-circles',
            'manage-weddings',
            'manage-courses',
            'manage-reports',
            'manage-vendors',
            'manage-counseling',
            'manage-community',
            'view-admin-dashboard',
            'view-audit-logs',
            'manage-refunds',
            // Recommender
            'add-candidates',
            'edit-candidates',
            'make-recommendations',
            'view-recommender-dashboard',
            // User
            'enroll-courses',
            'join-circles',
            'register-weddings',
            'submit-reports',
            'book-counseling',
            'post-community',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $recommender = Role::firstOrCreate(['name' => 'recommender', 'guard_name' => 'web']);
        $recommender->syncPermissions([
            'add-candidates', 'edit-candidates',
            'make-recommendations', 'view-recommender-dashboard',
            'enroll-courses', 'join-circles',
            'register-weddings', 'submit-reports',
            'book-counseling', 'post-community',
        ]);

        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions([
            'enroll-courses', 'join-circles',
            'register-weddings', 'submit-reports',
            'book-counseling', 'post-community',
        ]);
    }
}

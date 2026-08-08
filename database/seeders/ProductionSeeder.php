<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Production seeder — creates ONLY what a live site needs to start:
 * roles, permissions, and the super-admin account. No demo/test data.
 *
 * Run on live with:  php artisan migrate:fresh --seeder=ProductionSeeder
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedAdmin();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'super-admin',     'description' => 'Full access to all features'],
            ['name' => 'project-manager', 'description' => 'Manage clients, projects, tasks, and approvals'],
            ['name' => '3d-artist',       'description' => 'Create 3D assets and graphics'],
            ['name' => 'video-editor',    'description' => 'Create and edit video content'],
            ['name' => 'marketer',        'description' => 'Manage marketing tasks and campaigns'],
            ['name' => 'client',          'description' => 'View and approve content, leave feedback'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name'], 'guard_name' => 'web'], $r);
        }
    }

    private function seedPermissions(): void
    {
        $permissions = [
            'manage-clients', 'manage-projects', 'manage-tasks', 'manage-invoices',
            'manage-team', 'manage-salaries', 'manage-roles', 'manage-settings',
            'approve-content', 'view-reports',
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $defaults = [
            'super-admin'     => $permissions,
            'project-manager' => ['manage-clients', 'manage-projects', 'manage-tasks', 'manage-invoices', 'view-reports'],
            '3d-artist'       => ['manage-tasks'],
            'video-editor'    => ['manage-tasks'],
            'marketer'        => ['manage-tasks'],
            'client'          => ['approve-content'],
        ];
        foreach ($defaults as $role => $perms) {
            Role::findByName($role, 'web')->syncPermissions($perms);
        }
    }

    private function seedAdmin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'noorealamdev@gmail.com'],
            ['name' => 'Noor E Alam', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $admin->syncRoles(['super-admin']);
    }
}

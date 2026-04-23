<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            throw new \Exception('Permissions is empty, please run shield:generate first');
        }

        // roles
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate([
                'name' => $roleEnum->value,
            ]);
        }

        $superAdmin = Role::findByName(RoleEnum::Super_Admin->value);
        $admin      = Role::findByName(RoleEnum::Admin->value);
        $editor     = Role::findByName(RoleEnum::Editor->value);
        $user       = Role::findByName(RoleEnum::User->value);

        // super-admin 全權
        $superAdmin->syncPermissions($permissions);

        // admin（除了敏感操作）
        $admin->syncPermissions(
            $permissions->filter(fn ($p) =>
                !str_contains($p->name, 'delete_user') &&
                !str_contains($p->name, 'ban_user')
            )
        );

        // editor（內容相關）
        $editor->syncPermissions(
            $permissions->filter(fn ($p) =>
                str_contains($p->name, 'post')
            )
        );

        // user（最小權限）
        $user->syncPermissions([
            'ViewAny:Post',
            'View:Post',
        ]);
    }
}

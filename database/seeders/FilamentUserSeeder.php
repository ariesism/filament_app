<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FilamentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $superAdmin = User::updateOrCreate(
            ['email' => 'edison@example.com'],
            [
                'name' => 'Edison',
                'password' => bcrypt('password123'),
            ]
        );
        $superAdmin->syncRoles([RoleEnum::Super_Admin->value]);

        $roles = array_filter(
            RoleEnum::cases(),
            fn ($role) => $role !== RoleEnum::Super_Admin
        );

        foreach ($roles as $roleEnum) {

            $role = Role::firstOrCreate([
                'name' => $roleEnum->value,
            ]);

            $user = User::firstOrCreate(
                ['email' => $roleEnum->value . '@example.com'],
                [
                    'name' => ucfirst($roleEnum->value),
                    'password' => bcrypt('password123'),
                ]
            );

            $user->syncRoles([$role]);
        }

        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->syncRoles([[RoleEnum::User->value]]);
            });
    }
}

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'admin@agsoftweb.local'],
            [
                'name' => 'Administrador',
                'nickname' => 'admin',
                'password' => 'admin',
                'role' => Role::Admin,
            ],
        );

        User::query()->each(function (User $user): void {
            $user->password = 'admin';
            $user->save();
        });
    }
}

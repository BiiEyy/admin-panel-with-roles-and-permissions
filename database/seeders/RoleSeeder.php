<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Role::truncate();

        $permissions = Permission::get();

        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Admin Role',
        ]);

        foreach ($permissions as $permission) {
            $admin->givePermissionTo($permission);
        }

        Role::create([
            'name' => 'User',
            'description' => 'User Role',
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

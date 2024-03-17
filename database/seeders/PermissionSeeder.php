<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Permission::truncate();

        $permissions = [
            [
                'name' => 'Manage User - Read',
                'description' => 'Admin Tools - Manage User - Read'
            ],
            [
                'name' => 'Manage User - Create',
                'description' => 'Admin Tools - Manage User - Create'
            ],
            [
                'name' => 'Manage User - Edit',
                'description' => 'Admin Tools - Manage User - Edit'
            ],
            [
                'name' => 'Manage User - Delete',
                'description' => 'Admin Tools - Manage User - Delete'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
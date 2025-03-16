<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Contracts\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        
        // crear permisos
        $createProducts = Permission::create(['name' => 'create_products']);
        $editProducts = Permission::create(['name' => 'edit_products']);
        $deleteProducts = Permission::create(['name' => 'delete_products']);

        // asignar permisos a rol de admin
        $adminRole->givePermissionTo([$createProducts, $editProducts, $deleteProducts]);
    }
}

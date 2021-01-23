<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
         // create permissions
         Permission::create(['name' => 'create_order', 'display_name' => 'Walnut Order->WB New Po', 'guard_name' => 'wb']);

         // create roles and assign created permissions
 
         $role = Role::create(['name' => 'super-admin', 'guard_name' => 'wb']);
         $role->givePermissionTo(Permission::where('guard_name', 'wb')->get());
    }
}

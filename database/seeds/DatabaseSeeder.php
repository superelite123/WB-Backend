<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(RolesTabelSeeder::class);
        // $this->call(UsersTableSeeder::class);
        // Ask for db migration refresh, default is no
        Permission::create(['name' => 'Product Type']);
        // $permissions = [
        //     'add Order',
        //     'edit Order',
        //     'view Order',
        //     'delete Order',
        //     'add Harvest',
        //     'edit Harvest',
        //     'view Harvest',
        //     'delte Harvest',
        //     'dry Harvest',
        //     'reweight Harvest',
        //     'Processing Control',
        //     'Location',
        //     'Customer Relations',
        //     'Finished Goods',
        // ];
        // foreach($permissions as $permission)
        // {
        //     Permission::create(['name' => $permission]);
        // }

        // Seed the default permissions
        // $permissions = [
        //     'view_users',
        //     'add_users',
        //     'edit_users',
        //     'delete_users',

        //     'view_roles',
        //     'add_roles',
        //     'edit_roles',
        //     'delete_roles',

        //     'view_posts',
        //     'add_posts',
        //     'edit_posts',
        //     'delete_posts',
        // ];

        // foreach ($permissions as $perms) {
        //     Permission::firstOrCreate(['name' => $perms]);
        // }

        // $this->command->info('Default Permissions added.');

        // // Confirm roles needed
        // if ($this->command->confirm('Create Roles for user, default is admin and user? [y|N]', true)) {

        //     // Ask for roles from input
        //     $input_roles = $this->command->ask('Enter roles in comma separate format.', 'Admin,User');

        //     // Explode roles
        //     $roles_array = explode(',', $input_roles);

        //     // add roles
        //     foreach($roles_array as $role) {
        //         $role = Role::firstOrCreate(['name' => trim($role)]);

        //         if( $role->name == 'admin' ) {
        //             // assign all permissions
        //             $role->syncPermissions(Permission::all());
        //             $this->command->info('Admin granted all the permissions');
        //         } else {
        //             // for others by default only read access
        //             $role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
        //         }

        //         // create one user for each role
        //         $this->createUser($role);
        //     }

        //     $this->command->info('Roles ' . $input_roles . ' added successfully');

        // } else {
        //     Role::firstOrCreate(['name' => 'User']);
        //     $this->command->info('Added only default user role.');
        // }

    }

    /**
     * Create a user with given role
     *
     * @param $role
     */
    private function createUser($role)
    {
        $user = factory(User::class)->create();
        $user->assignRole($role->name);

        if( $role->name == 'Admin' ) {
            $this->command->info('Here is your admin details to login:');
            $this->command->warn($user->email);
            $this->command->warn('Password is "secret"');
        }
    }
}

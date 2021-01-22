<?php

use Illuminate\Database\Seeder;

use App\Models\Role;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $adminRole = Role::where('name','admin')->first();
        $managerRole = Role::where('name','manager')->first();
        $salesRole = Role::where('name','sales')->first();

        $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin')
        ]);
        
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@manager.com',
            'password' => bcrypt('manager')
        ]);
        
        $sales = User::create([
            'name' => 'Sales',
            'email' => 'sales@sales.com',
            'password' => bcrypt('sales')
        ]);

        $admin->roles()->attach($adminRole);
        $manager->roles()->attach($managerRole);
        $sales->roles()->attach($salesRole);
    }
}

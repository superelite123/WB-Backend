<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'add Order',
            'edit Order',
            'view Order',
            'delete Order',
            'add Harvest',
            'edit Harvest',
            'view Harvest',
            'delte Harvest',
            'dry Harvest',
            'reweight Harvest',
            'Processing Control',
            'Location',
            'Customer Relations',
            'Finished Goods',
        ];
        foreach($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }
    }
}

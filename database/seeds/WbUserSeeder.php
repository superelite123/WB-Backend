<?php

use Illuminate\Database\Seeder;
use App\Wb\WbUser;

class WbUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = WbUser::create([
            'name' => 'Administrator',
            'email' => 'superadmin@pcrealms.com',
            'password' => '$2y$10$zXME/I9hdhz/D7NxAH2urOJuacwoW9peE/qGY9pu41YUzFFrl/EK6', // password
        ]);
        $admin->assignRole('super-admin');
    }
}

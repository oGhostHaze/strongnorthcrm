<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_user = new Role();
        $role_user->name = 'User';
        $role_user->description = 'Normal User';
        $role_user->save();

        $role_user = new Role();
        $role_user->name = 'Admin';
        $role_user->description = 'Admin User';
        $role_user->save();

    }
}

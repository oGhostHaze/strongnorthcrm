<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_role = Role::where('name', 'User')->first();
        $admin_role = Role::where('name', 'Admin')->first();

        $user = User::where('email', 'strongnorth9319@gmail.com')->first();
        $admin = User::where('email', 'joshua070915@gmail.com')->first();

        $user->roles()->attach($user_role);
        $admin->roles()->attach($admin_role);

    }
}

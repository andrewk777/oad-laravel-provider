<?php

use Illuminate\Database\Seeder;

class UsersRolesPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users_roles_permissions')->delete();
        
        \DB::table('users_roles_permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'users_roles_id' => 1,
                'sections_id' => 1,
                'permission' => 'full',
            ),
            1 => 
            array (
                'id' => 2,
                'users_roles_id' => 1,
                'sections_id' => 2,
                'permission' => 'full',
            ),
            2 => 
            array (
                'id' => 3,
                'users_roles_id' => 1,
                'sections_id' => 3,
                'permission' => 'full',
            ),
        ));
        
        
    }
}
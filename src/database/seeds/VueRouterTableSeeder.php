<?php

use Illuminate\Database\Seeder;

class VueRouterTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vue_router')->delete();
        
        \DB::table('vue_router')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'users',
                'path' => '/admin/users',
                'componentPath' => '/pages/users/Users',
                'default' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'user-roles',
                'path' => '/admin/user-roles',
                'componentPath' => '/pages/users/UserRoles',
                'default' => NULL,
            ),
            2 => 
            array (
                'id' => 14,
                'name' => 'dashboard',
                'path' => '/dashboard',
                'componentPath' => '/pages/dashboard/Dashboard',
                'default' => 1,
            )
        ));
        
        
    }
}
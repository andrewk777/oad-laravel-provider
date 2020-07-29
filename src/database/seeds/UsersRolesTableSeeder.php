<?php

use Illuminate\Database\Seeder;

class UsersRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users_roles')->delete();
        
        \DB::table('users_roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Developer',
                'user_updated' => NULL,
                'user_created' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Admin',
                'user_updated' => '2',
                'user_created' => NULL,
                'created_at' => NULL,
                'updated_at' => '2020-05-19 18:27:54',
            ),
        ));
        
        
    }
}
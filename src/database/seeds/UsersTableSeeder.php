<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'hash' => 'e7fdd8b0-6165-11ea-952d-1b3d5a6a7df0',
                'name' => 'Andrew',
                'email' => 'andrew@oadsoft.com',
                'email_verified_at' => '2020-03-08 17:12:00',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'roles_id' => 1,
                'sys_access' => 1,
                'remember_token' => '',
                'user_updated' => '1',
                'user_created' => '',
                'created_at' => '2020-03-08 17:12:00',
                'updated_at' => '2020-03-08 17:12:00',
            ),
        ));
        
        
    }
}
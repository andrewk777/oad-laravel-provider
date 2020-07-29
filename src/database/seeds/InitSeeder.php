<?php

use Illuminate\Database\Seeder;

class InitSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    protected $counter = 0;

    public function run() {
        $this->call(UsersTableSeeder::class);
        $this->call(SectionsTableSeeder::class);
        $this->call(VueRouterTableSeeder::class);
        $this->call(UsersRolesTableSeeder::class);
        $this->call(UsersRolesPermissionsTableSeeder::class);
    }

}

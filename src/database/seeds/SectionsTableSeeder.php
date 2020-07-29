<?php

use Illuminate\Database\Seeder;

class SectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sections')->delete();
        
        \DB::table('sections')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => NULL,
                'text' => 'Admin',
                'vue_router_id' => '',
                'cssClass' => 'fal fa-cogs',
                'type' => 'menu',
                'access_options' => 'none,view',
                'sort_order' => 100,
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 1,
                'text' => 'Users',
                'vue_router_id' => '1',
                'cssClass' => '',
                'type' => 'menu',
                'access_options' => 'none,view',
                'sort_order' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 1,
                'text' => 'Roles',
                'vue_router_id' => '2',
                'cssClass' => NULL,
                'type' => 'menu',
                'access_options' => 'none,full',
                'sort_order' => 2,
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => NULL,
                'text' => 'Dashboard',
                'vue_router_id' => '14',
                'cssClass' => 'fal fa-chart-bar',
                'type' => 'menu',
                'access_options' => 'view',
                'sort_order' => 1,
            ),
        ));
        
        
    }
}
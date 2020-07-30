<?php

namespace OADSOFT\SPA;

use Illuminate\Support\ServiceProvider;

class OADServiceProvider extends ServiceProvider {

    public function boot() {

        //delete .env and .env.example
        //create and populate new .env

        //loading web and api routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        //loading views
        $this->loadViewsFrom(__DIR__ . '/views' , 'oadspa');

        //deleting default migrations
        $migration_files = array_diff(scandir(database_path()), array('..', '.'));
        foreach ($migration_files as $file_name) {
            if (strstr($file_name,'users_table')) {
                unlink(database_path($file_name));
            }
        }

        //deleting default files
        unlink(base_path('.env'));
        unlink(base_path('.env.example'));

        // deleting default User Model File
        if (file_exists(app_path('User.php'))) { 
            unlink(app_path('User.php'));
        }

        $this->publishes([
            __DIR__.'/Commands' => app_path('Console/Commands'),
            __DIR__.'/database/migrations' => base_path('database/migrations'),
            __DIR__.'/database/seeds' => base_path('database/seeds'),
            __DIR__.'/Exceptions' => app_path('Exceptions'),
            __DIR__.'/Http/Controllers' => app_path('Http/Controllers'),
            __DIR__.'/Models' => app_path('Models'),
            __DIR__.'/Notifications' => app_path('Notifications'),
            __DIR__.'/Traits' => app_path('Traits'),
        ]);

        //updating auth config to use a proper user model
        config(['auth.providers.users.model' => App\Models\User::class ]);

    }

    public function register() {
        
    }

}
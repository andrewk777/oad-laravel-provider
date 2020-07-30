<?php

namespace OADSOFT\SPA;

use Illuminate\Support\ServiceProvider;

class OADServiceProvider extends ServiceProvider {

    public function boot() {

        //loading web and api routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        //loading views
        $this->loadViewsFrom(__DIR__ . '/views' , 'oadspa');        

        //this should run only once
        if (file_exists(app_path('User.php'))) {

            // deleting default User Model File
            unlink(app_path('User.php'));

            //deleting default env files
            if (file_exists(base_path('.env.example'))) unlink(base_path('.env.example'));

            //deleting default migrations
            $migration_files = array_diff(scandir(database_path('migrations')), array('..', '.'));
            foreach ($migration_files as $file_name) {
                if (strstr($file_name,'users_table')) {
                    unlink(database_path('migrations/' . $file_name));
                }
            }

            // deleting default User Model File
            if (file_exists(config_path('auth.php'))) unlink(config_path('auth.php'));

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
            __DIR__.'/config' => config_path(),
        ]);

    }

    public function register() {
        
    }

}
<?php

namespace OADSOFT\SPA;

use Illuminate\Support\ServiceProvider;

class OADServiceProvider extends ServiceProvider {

    public function boot() {

        //loading web and api routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');     
        
        $this->loadViewsFrom(__DIR__.'/../publish/resources/views', 'oadsoft');

        $this->publishes([
            __DIR__.'/../publish/resources/views' => resource_path('views/oadsoft'),
        ]);

        $this->publishes([
            __DIR__.'/../publish/Http/Controllers'      => app_path('Http/Controllers'),
            __DIR__.'/../publish/Console'               => app_path('Console')
        ]);

    }

    public function register() {
        
    }

}
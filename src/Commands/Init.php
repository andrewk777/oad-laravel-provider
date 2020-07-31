<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OAD\Router;
use DB, View;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature    = 'oad {oad_command}';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Initial SPA System Setup';

     /**
      * Create a new command instance.
      *
      * @return void
      */
     public function __construct()
     {
         parent::__construct();
     }

     /**
      * Execute the console command.
      *
      * @return mixed
      */
     public function handle()
     {

        switch($this->argument('oad_command')) {
            case 'seed':
                $this->seed();
            break;
            case 'reseed':
                $this->dropTables();
                $this->seed();
            break;
            case 'dropTables':
                $this->dropTables();
            break;
            case 'genenv':
                $this->genenv();
            break;
            case 'genVueRoutes':
                $this->genVueRoutes();
            break;
        }    

     }

     private function dropTables() {

        $colname = 'Tables_in_' . config('database.connections.mysql.database');
        $droplist = [];

        $tables = DB::select('SHOW TABLES');
        if (count($tables)) {
            foreach($tables as $table) {
                $droplist[] = $table->$colname;
            }
            $droplist = implode(',', $droplist);
            DB::beginTransaction();
            DB::statement("DROP TABLE $droplist");
        }

        $this->info('The following tables dropped:' . $droplist);
       
     }

     private function seed() {
        $this->call('migrate');
        $this->info('Migration done');
        $this->call('db:seed',['--class' => 'InitSeeder']);
        $this->info('Seeding Done');
        $this->genVueRoutes();
     }

     private function genenv() {
        $json = file_get_contents(base_path('oad-conf.json'));
        file_put_contents(base_path('.env'), View::make('console.env', json_decode($json,true )));
     }

     private function genVueRoutes() {

       $routes = Router::all();
 
       $default = $routes->filter(function($item) {
           if ($item->default) return $item;
       });
       $appDefaultUrl = array_values($default->toArray())[0]['path'];

       file_put_contents(resource_path('js/routes.js'), View::make('console.viewRouter', [ 'backendRoutes' => $routes ] ));
       file_put_contents(resource_path('js/config.js'), View::make('console.condfigjs', [ 'appDefaultUrl' => $appDefaultUrl ]));
        
        $this->info('Vue Routes Generated');
        
     }
}

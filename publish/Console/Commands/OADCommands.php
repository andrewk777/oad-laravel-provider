<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OAD\Router;
use DB, View;

class OADCommands extends Command
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

    protected $path = false;
    protected $apiKey = false;
    protected $fileName = false;

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
                $this->call('migrate');
                $this->info('Migration done');
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
            case 'mkController':
                $this->mkController();
            break;
            case 'mkModel':
                $this->mkModel();
            break;
            case 'mkVueResource':
                $this->mkVueIndex();
                $this->mkVueForm();
            break;
            case 'mkVueIndex':
                $this->mkVueIndex();
            break;
            case 'mkVueForm':
                $this->mkVueForm();
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
        $this->call('db:seed',['--class' => 'InitSeeder']);
        $this->info('Seeding Done');
     }

     private function genenv() {
        $json = file_get_contents(base_path('oad-conf.json'));
        file_put_contents(base_path('.env'), View::make('console.env::oadsoft', json_decode($json,true )));
     }

     public function mkController() {

        $params_folder = '';

        $controller = $this->ask('Name of the controller');
        $model      = $this->ask('Enter Model');
        $folder     = $this->ask('Enter Name of a folder or leave blank for no folder');
        $apiKey= $this->ask('Enter api array key or leave blank to skip');
        if ($apiKey) {
            $apiType    = $this->ask('Is it a resource (type anything for yes)');
            $apiType    = $apiType ? 'resource' : 'single';
        }
        $section_slug   = $this->ask('Section Slug ');
        

        if ($folder) {
            $dir = app_path('Http/Controllers/' . $folder);
            if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
                mkdir( $dir );
            }
            $params_folder = '\\' . $folder;
            $folder .= '/';
            
        }
        $params = [
            'controller_name'   => $controller,
            'model_name'        => $model,
            'folder'            => $params_folder,
            'section_slug'      => $section_slug
        ];
        file_put_contents(
            app_path('Http/Controllers/' . $folder . $controller . '.php'),
            '<?php' . PHP_EOL . View::make('console.controller::oadsoft', $params)
        );

        if ($apiKey) {
            $api_routes_file = file_get_contents(base_path('routes/api.php'));
            
            file_put_contents(
                base_path('routes/api.php'),
                str_replace(
                    '$routes = [', 
                    "\$routes = [" . PHP_EOL . 
                        "    '{$apiKey}' => [" . PHP_EOL . 
                        "       'type'  => '{$apiType}'," . PHP_EOL . 
                        "       'controller'    =>'{$controller}'" . PHP_EOL .
                        "   ],", $api_routes_file)
            );
        }

        $this->info('Controller ' . $folder . $controller . ' created');

    }

     public function mkModel() {

        $model_name     = $this->ask('Model Name');
        $folder         = $this->ask('Model Folder (leave blank for root) ');
        $table_name     = $this->ask('Table Name');
        $name_space     = '';

        if ($folder) {
            $dir = app_path('Models/' . $folder);
            if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
                mkdir( $dir );
            }
            $folder .= '/';
            $name_space = '\\' . $folder;
            
        }
        $params = [
            'model_name'    => $model_name,
            'table_name'    => $table_name,
            'name_space'    => $name_space
        ];
        file_put_contents(
            app_path('Models/' . $folder . $model_name . '.php'),
            '<?php' . PHP_EOL . View::make('console.model::oadsoft', $params)
        );

        $this->info('Model ' . $folder . $model_name . ' created');

    }
    
    public function mkVueIndex() {

        $this->path         = $this->ask("Folder or Path From js/views/backend/");
        if ($this->path)    $this->path .= '/';
        $this->apiKey       = $this->ask('API key: ');
        $this->fileName     = $this->apiKey . 'Index';
        $title              = $this->ask('Vue Index Page Title: ');
        $dir                = resource_path( 'js/views/backend/' . $this->path );

        if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
            mkdir( $dir );
        }

        $params = [
            'title'         => $title,
            'resource'      => $this->apiKey,
            'ref'           => \Str::camel($this->apiKey)
        ];
            
        file_put_contents(
            $dir . $this->fileName . '.vue',
            '<?php' . PHP_EOL . View::make('console.vueIndex::oadsoft', $params)
        );

        $this->info("Vue Index 'js/views/backend/{$this->path}{$this->fileName}.vue' created");

    }

    public function mkVueForm() {
        
        $this->fileName      = $this->ask('Vue Form File Name (without .vue) - leave blank for default: ');

        if ($this->path === false) {
            $this->path       = $this->ask("Folder or Path From @/views/backend/");
            if ($this->path)    $this->path .= '/';
        }
            
        $title      = $this->ask('Vue Form Page Title: ');
        if ($this->apiKey === false)
            $this->apiKey   = $this->ask('Resource: ');
        if (!$this->fileName)
            $this->fileName     = $this->apiKey . 'Form';
        
        $dir = resource_path('js/views/backend/' . $this->path );
        if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
            mkdir( $dir );
        }

        $params = [
            'title'         => $title,
            'resource'      => $this->apiKey
        ];

        file_put_contents(
            $dir . $this->fileName . '.vue',
            '<?php' . PHP_EOL . View::make('console.vueForm::oadsoft', $params)
        );

        $this->info("Vue Form '@/views/backend/{$this->path}{$this->fileName}.vue' created");

    }

     private function genVueRoutes() {

       /* $routes = Router::all();
 
       $default = $routes->filter(function($item) {
           if ($item->default) return $item;
       });
       $appDefaultUrl = array_values($default->toArray())[0]['path'];

       file_put_contents(resource_path('js/routes.js'), View::make('console.viewRouter', [ 'backendRoutes' => $routes ] ));
       file_put_contents(resource_path('js/config.js'), View::make('console.condfigjs', [ 'appDefaultUrl' => $appDefaultUrl ]));
        
        $this->info('Vue Routes Generated'); */
        
     }
}

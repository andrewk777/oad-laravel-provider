<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use View, DB;

class MkModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

     protected $signature = '1111';
     public $settings = [];

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Creates all module components including migration,model,countroller,sections,routes';

 	public function __construct()
     {
         parent::__construct();
     }

 	public function handle()
    {
        $settings = [
            'create_migration'          => true,
            'migration_table_name'      => 'bug_logs', // i.e. 'companies'
            'hash_primary'              => true,
            'migration_fields'          => [ //needs work, not working now
              ],

            'create_model'              => true,
            'model_name'                => 'BugTracker', // i.e. 'Company'
            'form_fields'               => [ // (name)-(label)-(type - optional)
            ],

            'create_export'             => false, //create export class

            'create_controller'         => false,
            'controller_folder'         => '', // any controller subfolder i.e. Admin/ (make sure there is a slash at the end)
            'controller_name'           => 'BugLogController', //i.e. CompanyController

            'create_nav_menu'           => false,
            'menu_text'                 => 'Bug Tracker', // i.e. 'Company'
            'menu_parent_id'            => null, // i.e. '1'
            'menu_icon'                 => '', // i.e. 'fal fa-user-friends'
            'menu_access_options'       => 'none,view', //i.e. 'none,view,full'
            'menu_type'                 => 'menu', // i.e. 'menu'
            'menu_sort_order'           => '10', //i.e. '1'
            'menu_router_id'            => '', //will be overwritten if create_new_router is set to true

            'create_new_router'         => false,
            'router_name'               => 'dashboard', // i.e 'users'
            'router_path'               => '/dashboard', //i.e. '/admin/users'
            'router_component_path'     => '/pages/dashboard/Dashboard', //i.e. '/pages/users/Users'

            'create_vue_components'     => false,
            'vue_components_types'      => 'just_form', // options: 'index_n_form' / 'just_form'
            'vue_components_folder'     => 'bugtracker/', // i.e. 'users/' -> this will create a folder: 'resources\js\Backend\pages\users' or leave blank
            'vue_component_index_name'  => 'Bugtracker', // i.e. 'invoices'
            'vue_component_form_name'   => 'BugtrackerAE', // i.e. 'invoicesAE' or 'Company' - for a single form page
            'page_title_plural'         => 'Bug Tracker', //i.e. 'Invoices'
            'page_title_single'         => 'Bug Tracker',
            'base_api_route'            => 'bugtracker', // i.e. 'users' - where index table will be sending request and key for api.php array
            'vue_table_columns'         => [ //needs work, not using now
                [
                ]
            ]


        ];

        if ($settings['create_migration']) {
            $this->call('make:migration', [ 'name' => 'create_'. $settings['migration_table_name'] . '_table' ]);
        }

        if (count($settings['form_fields'])) {
            $formatted_fields = [];
            foreach ($settings['form_fields'] as $item) {
                list($name,$label,$type) = explode('-',$item);
                $formatted_fields[] = [
                    'name'  => $name,
                    'type'  => ($type ? $type : 'text'),
                    'label' => $label
                ];
            }
            $settings['form_fields'] = $formatted_fields;
        }

        if ($settings['create_model']) {
	        file_put_contents(app_path('Models/' . $settings['model_name'] . '.php'), '<?php' . PHP_EOL . View::make('console.model', $settings));
        }

        if ($settings['create_controller']) {

            if ($settings['controller_folder']) {
                $folder_name = str_replace('/','',$settings['controller_folder']);
                $dir = app_path('Http/Controllers/' . $folder_name);
                if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
                    mkdir( $dir );
                }
            }

			file_put_contents(
                app_path('Http/Controllers/' . $settings['controller_folder'] . $settings['controller_name'] . '.php'),
                '<?php' . PHP_EOL . View::make('console.controller', $settings)
            );
        }

        if ($settings['create_new_router']) {
            $settings['menu_router_id'] = DB::table('vue_router')->insertGetId([
                'name' => $settings['router_name'],
                'path' => $settings['router_path'],
                'componentPath' => $settings['router_component_path'],
            ]);

        }

        if ($settings['create_nav_menu']) {

            DB::table('sections')->insert([
                'parent_id'     => $settings['menu_parent_id'],
                'text'          => $settings['menu_text'],
                'vue_router_id' => $settings['menu_router_id'],
                'cssClass'      => $settings['menu_icon'],
                'type'          => $settings['menu_type'],
                'access_options'=> $settings['menu_access_options'],
                'sort_order'    => $settings['menu_sort_order']
            ]);

        }

        if ($settings['create_vue_components']) {

            /*if (!is_dir('../../resources/js/Backend' . $dir)) {
                mkdir('../../resources/js/Backend' . $dir);
            }*/

            $folder_name = str_replace('/','',$settings['vue_components_folder']);
            $app_path = 'resources/js/Backend/pages/';
            $dir = $app_path . $folder_name;

            if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
                mkdir( $dir );
            }

            switch ($settings['vue_components_types']) {
                case 'index_n_form':
                    file_put_contents($app_path . $settings['vue_components_folder'] . $settings['vue_component_index_name'] . '.vue', View::make('console.vueIndex', $settings));
                    file_put_contents($app_path . $settings['vue_components_folder'] . $settings['vue_component_form_name'] .'.vue', View::make('console.vueAE', $settings));
                break;
                case 'just_form':
                    file_put_contents($app_path . $settings['vue_components_folder'] . $settings['vue_component_form_name'] .'.vue', View::make('console.vueSingleForm', $settings));
                break;
            }

        }
        if ($settings['create_export']) {
            $this->call('make:export ' . $settings['model_name'] . 'Export --model=Models\\' . $settings['model_name'] );
        }
        $this->call('vue:genRoutes');
        $this->call('config:cache');

        $this->error('Finished, Dont forget to update routes "api.php" && migration');

     }
}

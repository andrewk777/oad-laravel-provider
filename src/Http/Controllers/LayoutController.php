<?php

namespace OADSOFT\SPA\Http\Controllers;

use Auth;
use App\Models\OAD\Section;
use App\Http\Controllers\Controller;

class LayoutController extends Controller
{

    private $check_permissions;

    public function full_menu($check_permissions = true, $include_secions = false) {

        $this->check_permissions = Auth::user()->roles_id == 1 ? false : $check_permissions;

        return response()->json([
            'menu_primary'      => $this->primary_menu($check_permissions),
            'menu_secondary'    => $this->secondary_menu($check_permissions,$include_secions)
        ]);
    }

    public function set_permission_filter($val = true) {

        $this->check_permissions = $val;
        return $this;

    }

    public function primary_menu() {

        return $this->permission_filter(
            Section::with('routes')->whereNull('parent_id')->where('type','menu')->orderBY('sort_order')->get()
        );
                
    }

    public function secondary_menu() {

        $sections = $this->permission_filter(
            Section::with('routes')->whereNotNull('parent_id')->where('type','menu')->orderBY('parent_id')->orderBY('sort_order')->get()
        );
        
        return $sections->groupBy('parent_id');
    }

    private function permission_filter($sections) {

        if ($this->check_permissions) {
            $permissions = \User::get_permissions();
            $sections->filter(function($record) use ($permissions) {
                return !empty($permissions[$record->id]) && $permissions[$record->id] != 'none';
            });
        }
        return $sections;
    }

    public function sections_tree($parent_id = null) {
        
        $items = Section::where('parent_id',$parent_id)->orderBy('sort_order')->get();
        $permissions = \User::get_permissions();

        if ($items->count()) {
            
            return $items->transform(function($item) use ($permissions) {

                if (!$this->check_permissions || (!empty($permissions[$item->id]) && $permissions[$item->id] != 'none' )) {

                    $item->access_options   = collect(explode(',',$item->access_options))->transform(function($item) { 
                        return [
                            'code'  => $item,
                            'label' => ucfirst($item) 
                        ];
                    });

                    $item->children         = $this->sections_tree($item->id);

                    return $item;

                }                

            });
        }
        
        return false;
    }

}

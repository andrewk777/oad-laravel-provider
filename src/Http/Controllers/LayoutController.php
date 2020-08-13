<?php

namespace OADSOFT\SPA\Http\Controllers;

use Auth;
use App\Models\OAD\Section;
use App\Http\Controllers\Controller;

class LayoutController extends Controller
{

    private $check_permissions;

    public function index($check_permissions = true, $include_secions = false) {

        $this->check_permissions = Auth::user()->roles_id == 1 ? false : $check_permissions;

        return response()->json([
            'menu_primary'      => $this->primary_menu($check_permissions),
            'menu_secondary'    => $this->secondary_menu($check_permissions,$include_secions)
        ]);
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

}

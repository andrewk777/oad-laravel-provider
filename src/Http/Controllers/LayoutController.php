<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Section;

class LayoutController extends Controller
{

    private $check_permissions;

    public function index($check_permissions = true) {

        $this->check_permissions = Auth::user()->roles_id == 1 ? false : $check_permissions;
        return response()->json( $this->menu() );
    }

    public function menu($section_prefix = 'section_') {

        return $this->gen_tree($section_prefix,['type' => 'menu'], User::get_permissions());
    }

    public function gen_tree($section_prefix,$where = [], $permissions = []) {

        $items = Section::with('routes');
        if (count($where)) $items->where($where);
        $items = $items->get()->toArray();

		$tree =$this->buildTree($section_prefix,$items,$permissions);

		return $this->sortTree($tree);
	}

    public function buildTree($section_prefix,array &$elements, $permissions = [], $parentId = 0, $limitReturn = true) {

	    $branch = array();
	    foreach ($elements as &$element) {

	        if ($element['parent_id'] == $parentId) {

	            $children = $this->buildTree($section_prefix,$elements, $permissions, $element['id'], $limitReturn);
				$element['children'] = $children ? $children : [];
                $element['route'] = $element['routes'] ? $element['routes']['path'] : null;
                $element['access_options'] = $element['access_options'] ? explode(',',$element['access_options']) : [];
				$element['permission'] = !empty($permissions[$element['id']]) ? $permissions[$element['id']] : [];
                if ((array_key_exists($element['id'], $permissions) && $permissions[$element['id']] != 'none') || !$this->check_permissions) {
                    $branch[$section_prefix . $element['id']] = $limitReturn ? [
                        'id'                => $element['id'],
                        'text'              => $element['text'],
                        'cssClass'          => $element['cssClass'],
                        'route'             => $element['route'],
                        'access_options'    => $element['access_options'],
                        'sort_order'        => $element['sort_order'],
                        'children'          => $element['children']
                        ] : $element;
                }

	            unset($element);
	        }
	    }

	    return $branch;
	}

	public function sortTree(&$elements) {

		uasort($elements, function($a,$b) {
			return $a['sort_order'] <=> $b['sort_order'];
		});

	    foreach ($elements as &$element) {
			if ($element['children']) {
				$element['children'] = $this->sortTree($element['children']);
			}
	    }

		return $elements;

	}

    public function sectionsDfltPermissions() {
        return Section::selectRaw(" 'none' as text, id")->get()->pluck('text','id');
    }

}

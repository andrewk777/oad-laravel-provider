<?php

namespace OADSOFT\SPA\Http\Controllers\DEV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OAD\Section;

class NavController extends Controller {

    protected function flatten_menu_items($array, $parent_id = null) {

        if (!count($array)) return false;
        
        $result = [];

        foreach ($array as $key => $value) {
            $value['sort_order'] = $key;
            $value['parent_id'] = $parent_id;
            if (count($value['children'])) {
                $result = array_merge($result, $this->flatten_menu_items($value['children'], $value['id']));
            }
            unset($value['children']);
            $result[] = $value;
        }

        return $result;

    }
    
    public function save(Request $request) {

        $flatten_tree = $this->flatten_menu_items($request->tree);

        foreach ($flatten_tree as $item) 
            Section::updateOrCreate( ['id' => $item->id ], $item );

            response()->json([ 'status' => 'success', 'res' => 'Update Saved']);

    }

    public function delete(Request $request) {

        Section::find($request->hash)->delete();

        response()->json([ 'status' => 'success', 'res' => 'Menu Item Deleted']);

    }

}


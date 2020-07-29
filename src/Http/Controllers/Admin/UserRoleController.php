<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\OADController;
use Illuminate\Http\Request;
use App\Http\Controllers\Layout;
use App\Models\UserRolePermission;
use App\Models\User;

class UserRoleController extends OADController
{

	protected $model = 'App\Models\UserRole';

	public function index(Request $request)
    {

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model(), //class
            config('vars.table.editDelete'),
            function($q) {
                return $q->list();
            }
        );

        return response()->json( $response );
    }

	public function show(Request $request)
    {

        $model = $request->hash ? $this->model::find($request->hash) : new $this->model;

        $modelsNvalues  = $model->buildFields()->getFieldModelValues('id');
		$menu = new Layout();

        return response()->json(
            [
                'status'    => 'success',
                'hash'      => $request->hash,
                'fields'    => $model->form_fields,
                'models'    => $modelsNvalues,
				'extra'		=> [
					'permissions' => $menu->menu(),
					'sectionDflt' => $menu->sectionsDfltPermissions(),
					'permValues'  => UserRolePermission::where('users_roles_id',$request->hash)->get()->pluck('permission','sections_id')->toArray()
				]
            ],
            200
        );
    }

	public function store(Request $request) {

		$model = $request->hash ? $this->model::find($request->hash) : new $this->model;

        $model->validateForm($request->data)
              ->store([ 'id' => $request->hash ],  $request->data + ['items' => $request->items]);

	}

	public function destroy($hash) {

		if (User::where('roles_id',$hash)->count()) {
			return response()->json(['status' => 'error','res' => 'This Role is assigned to one or more users']);
		}

		if ($this->model::destroy($hash)) {
            UserRolePermission::where('users_roles_id',$hash)->delete();
			return response()->json(['status' => 'success', 'res' => 'Record deleted']);
		}

		return response()->json(['status' => 'error', 'res' => 'Failed to delete']);

    }


}

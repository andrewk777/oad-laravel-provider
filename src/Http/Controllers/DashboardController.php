<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\TableHelpers;

class DashboardController
{
    use TableHelpers;

    protected $model = 'App\Models\User';
    protected $customSearch = false;

    public function index(Request $request)
    {

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model(), //class
            config('vars.table.editDelete'),
            [], //model scope
            [], //relations,
            'queryExtention' //function exnending query
        );

        return response()->json( $response );
    }

}

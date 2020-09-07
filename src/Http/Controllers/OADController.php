<?php

namespace OADSOFT\SPA\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\TableHelpers;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class OADController extends Controller
{
    use TableHelpers;

    protected $filters;
	protected $model = '';

    public function index(Request $request)
    {

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model, //class
            config('vars.table.editDelete')
            //query extention
            //filters query
            //custom search queries
        );

        return response()->json( $response );
    }

    public function show(Request $request)
    {

        $model = $request->hash ? $this->model::find($request->hash) : new $this->model;

        $modelsNvalues  = $model->buildFields()->getFieldModelValues();

        return response()->json(
            [
                'status'    => 'success',
                'hash'      => $request->hash,
                'forms'    => [
                    'main'  => [
                        'fields'    => $model->form_fields,
                        'values'    => $modelsNvalues
                    ]
                    
                ]
            ], 
            200
        );

    }

    public function store(Request $request) {

        $model = $request->hash ? $this->model::where('hash',$request->hash)->first()  : new $this->model();
        
        $model->validateForm($request->forms)
              ->store([ 'hash' => $request->hash ], $request->data);

    }

    public function destroy($hash) {

        if ($this->model::destroy($hash)) {
            return response()->json(['status' => 'success', 'res' => 'Record deleted']);
        }

        return response()->json(['status' => 'error', 'res' => 'Failed to delete']);
    }

    public function list(Request $request) {

        $model = $this->model::select(['hash','name']);

        //lookup matching pair for value
        if ($request->hash) {
            return $model->where('hash',$request->hash)->get()->pluck('name','hash');
        }

        //perform search for results
        if ($request->search) {
            $model->where('name','LIKE','%' . $request->search . '%');
        }
        if ($request->clients_id) {
            $model->where('clients_id',$request->clients_id);
        }
        return $model->limit(10)->orderBy('name')->get()->pluck('name','hash');
    }

    public function export() {
        $modelName  = explode('\\',$this->model);
        $modelName  = end($modelName);
        $mPath      = 'App\\Exports\\' . $modelName . 'Export';
        $file_name  = $modelName . '-' . date('Y-m-d-h-i-s') . '.xlsx';
        Excel::store(new $mPath(), './tmp/' . $file_name);
        return $file_name;
    }

}

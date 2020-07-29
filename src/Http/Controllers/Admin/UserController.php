<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\OADController;
use Illuminate\Http\Request;
use Request as DRequest;
use Illuminate\Validation\Rule;

class UserController extends OADController
{

    protected $model = 'App\Models\User';

    public function listFilters($query) {

        $query->leftJoin('users_roles', 'users_roles.id', '=', 'users.roles_id');

        return $query;
    }

    public function index(Request $request)
    {

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model(), //class
            [ //action buttons
                [ 'action' => 'edit', 'text'   => 'Edit' ]
            ],
            ['list'], //model scope
            [], //relations
            'listFilters' //function exnending query
        );

        return response()->json( $response );
    }

    public function compFilters($query) {

        return $query;
    }

    public function show(Request $request)
    {
        $model = $request->hash ? $this->model::where('hash',$request->hash)->first() : new $this->model();

        $modelsNvalues = $model->buildFields()->getFieldModelValues('id');

        return response()->json(
            [
                'status'    => 'success',
                'hash'      => $request->hash,
                'fields'    => $model->form_fields,
                'models'    => $modelsNvalues,
            ], 200);
    }

    public function store(Request $request) {

        $model = $request->hash ? $this->model::where('hash',$request->hash)->first() : new $this->model();

        $valid_roles = [
                        'email' => [
                                'required',
                                'email',
                                Rule::unique('users')->ignore($model),
                            ],
                        'password' => [
                                'sometimes',
                                'nullable',
                                'min:6'
                            ]
                    ];
        if (!$request->hash) { // for new users
            $valid_roles['password'] = [
                'required',
                'min:6'
            ];
        }

        $model->validateForm($request->data,true,$valid_roles)
              ->store([ 'hash' => $request->hash ], $request->data);

    }

    public function destroy($hash) {
        return false;
    }

    public function listBuilder($model = '', $actions = [], $modelScopes = [], $relations = [], $queryExtention = false) {

        list($sort_by,$sort_dir)    = explode("|",DRequest::input('sort'));
        $page                       = DRequest::input('page');
        $per_page                   = DRequest::input('per_page');
        $skip                       = ($page - 1) * $per_page;
        $search                     = DRequest::input('search');
        $filters                    = DRequest::input('filters');
        $doFilters                  = false;
        $table                      = DRequest::input('config');
        $cols                       = [];
        $selectCols                 = [];
        $model_table                = $model::getTableName();

        //preparing columns to select from db
        foreach ($table['cols'] as $column) {
            if (!isset($column['noSelect'])) {
                $selectCols[] = isset($column['db']) ? $column['db'] . ' as ' .$column['name'] : $model_table.'.'.$column['name'];
                $cols[$column['name']] = isset($column['db']) ? $column['db'] : $model_table.'.'.$column['name'];
            }
        }

        $query = $model::select($selectCols);

        foreach ($modelScopes as $scope) {
            $query->$scope();
        }
        if (count($relations)) $query->with($relations);

        if ($queryExtention) $query = $this->$queryExtention($query);

        if ($search) { //go through all searchable queries
            $query->where(function($q) use ($table, $search,$cols) {
             foreach ($table['cols'] as $column) {
                 if (!(isset($column['searchable']) && $column['searchable'] === false)) {
                     $q->orWhere($cols[$column['name']],'LIKE','%' . $search . '%');
                 }
             }
                if ($this->customSearch) {
                    $search = htmlentities($search,ENT_QUOTES);
                    foreach ($this->customSearch as $rawSearch) {
                     $q->orWhereRaw( str_replace('?',$search,$rawSearch) );
                 }
                }
            });
        }
        if ($filters && is_array($filters)) {

            foreach ($filters as $field => $value) {
                if ($value) {
                    $value = htmlentities($value,ENT_QUOTES);
                    $query->where($field,'LIKE','%' . $value . '%');
                }
                // dump($query->toSql());
            }

        }

        $total      = $query->count();
        $last_page  = ceil($total / $per_page);
        $data       = $query->orderBy($sort_by, $sort_dir)->skip($skip)->take($per_page)->get();
        $from       = $skip + 1;
        $to         = $total < ($page * $per_page) ? $total : $page * $per_page;

        if ($actions) {

            $data = $data->transform(function($item) use ($actions) {


                $return = $item;
                $return['actions'] = $actions;
                return $return;

            })->toArray();

        }

        return [
            'pagination' => [
                'total'             => $total,
                'per_page'          => $per_page,
                'current_page'      => (int)$page,
                'last_page'         => $last_page,
                'next_page_url'     => '...',
                'prev_page_url'     => '...',
                'from'              => $from,
                'to'                => $to,
            ],
            'data'              => $data
        ];
    }

    public function list(Request $request) {
        $mPath = $this->model;
        $model = $mPath::list()->active()->select(['hash','name']);

        //lookup matching pair for value
        if ($request->hash) {

            return $model->where('hash',$request->hash)->get()->transform(function($item,$key) {
                return [
                    'code' => $item['hash'],
                    'label'  => $item['name']
                ];
            });
        }
        
        //perform search for results
        if ($request->search) {
            $model->where('name','LIKE','%' . $request->search . '%');
        }
        return $model->limit(10)->orderBy('name')->get()->transform(function($item, $key) {
            return [
                'code' => $item['hash'],
                'label'  => $item['name']
            ];
        })->toArray();
    }

}

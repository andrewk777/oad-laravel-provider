<?php
namespace App\Traits;

use Request;

trait TableHelpers {

    //generate response for vuetable query
    public function listBuilder($model = '', $actions = [], $queryExtention = false, $customFilters = [], $customSearch = false) {

        list($sort_by,$sort_dir)    = explode("|",Request::input('sort'));
        $page                       = Request::input('page');
        $per_page                   = Request::input('per_page');
        $skip                       = ($page - 1) * $per_page;
        $search                     = Request::input('search');
        $filters                    = Request::input('filters');
        $doFilters                  = false;
        $table                      = Request::input('config');
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

        if ($queryExtention && is_callable($queryExtention)) {
            $query = call_user_func($queryExtention,$query);
        }       

        if ($search) { //go through all searchable queries
            $query->where(function($q) use ($table,$search,$model_table,$customSearch) {
    			foreach ($table['cols'] as $column) {
    				if (!(isset($column['searchable']) && $column['searchable'] === false)) {
                        $searchBy = !empty($column['searchBy']) ? $column['searchBy'] : $column['db'] ?? $column['name'];
                        if (strstr($searchBy,'.')) {
                            list($table,$field) = explode('.',$searchBy);
                        } else {
                            $table = $model_table;
                            $field = $searchBy;
                        }
    					$q->orWhere($table.'.'.$field,'LIKE','%' . $search . '%');
    				}
                }
                if ($customSearch) {
                    $q = call_user_func($customSearch,$q,$search);
                }
            });
		}

        if ($filters && is_array($filters)) {

            foreach ($filters as $field => $value) {
                if ($value) {
                    if (array_key_exists($field, $customFilters)) {
                        $query = call_user_func($customFilters[$field],$query,$value);
                    } else {
                        $query->where($field,'LIKE','%' . $value . '%');
                    }
                }
            }

        }
        // dd($query->toSql());
        $total      = $query->count();
        $last_page  = ceil($total / $per_page);
        $data       = $query->orderBy($sort_by, $sort_dir)->skip($skip)->take($per_page)->get();
        $from       = $skip + 1;
        $to         = $total < ($page * $per_page) ? $total : $page * $per_page;

        
        $data = $data->transform(function($item) use ($actions) {

            $return    = $item->getAttributes();

            if (!empty($actions)) $return['actions'] = $actions; 
            
            return $return;

        })->toArray();

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

}

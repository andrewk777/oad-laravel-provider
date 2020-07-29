<?php

namespace App\Models\OAD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Models\File;

class OADModel extends Model {

    public $form_fields = [];

    protected $validated = false;
    protected $form_errors = [];
    protected $guarded = ['hash'];
    protected  $fileFields = [];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function buildFormFields($fields = []) {
        $arr = [];
        foreach ($fields as $field) {
           $arr[$field['key']] = $field;
           if ($field['type'] == 'file')
                $this->fileFields[] = $field['name'];
        }

        return $arr;
    }

    public function scopeJoinUser($query,$join_on_field) {
        $this_table = with(new $this)->getTable();
        $query->leftJoin('users' , $this_table . '.' . $join_on_field, '=', 'users.hash');
    }
    
    public function scopeJoinTable($query,$join_table,$using_column,$joint_table_column = 'hash') {
        $this_table = with(new $this)->getTable();
        $query->leftJoin($join_table , $this_table . '.' . $using_column, '=', $join_table.'.'.$joint_table_column );
    }

    public function getFieldModelValues($preimaryKey = 'hash') {
        $return = [];

        foreach ($this->form_fields as $arrKey => $field) {
            $objKey = $field['db_name'];
            if ($this->$preimaryKey) { //existing record
                if ($field['type'] != 'file') {
                    $return[$objKey] = $field['assignVal'] ? $this->$objKey : '';
                } else {
                    $files_arr = [];
                    for ($i = 0; $i < $this->files->count(); $i ++) {
                        if ($this->files[$i]->is_saved && $this->files[$i]->attachment_field == $objKey) {
                            $files_arr[] = [
                                'hash'  => $this->files[$i]->hash,
                                'name'  => $this->files[$i]->file_name
                            ];
                        }
                    }
                    $return[$objKey]= $files_arr;
                }

            } else { //new record
                $return[$objKey] = $field['dValue']!=='' ? $field['dValue'] : '';
            }
        }

        return $return;
    }

    public function validateForm( $data, $return_response = true, $rules = [], $attributes = [], $messages = []) {

        if (array_key_first($data) === 0 && is_array($data[0])) { //checking if array is stacked
            foreach ($data as $dataItem) {
                $this->validateForm($dataItem, $return_response, $rules, $attributes, $messages);
            }
        } else {

            $this->buildFields();

            $model_rules = [];
            foreach ($this->form_fields as $field) {
                if ($field['required']) {
                    $model_rules[$field['name']] = $field['required'];
                }
            }

            $rules = array_merge($model_rules,$rules);
            $validator = Validator::make($data, $rules);

            $model_attributes = [];
            foreach ($this->form_fields as $field) {
                $model_attributes[$field['name']] = $field['label'] ? $field['label'] : $field['placeholder']; //placeholder for checkboxes
            }
            $attributes = array_merge($model_attributes,$attributes);
            $validator->setAttributeNames($attributes);

            $validator->setCustomMessages(count($messages) ? $messages : ['required' => ':attribute is required']);

            $this->validated = !$validator->fails();

            if (!$this->validated && $return_response) {

                abort(
                    response()->json([ 'status' => 'error', 'res' => implode('<br>',$validator->errors()->all()) ], 200)
                );

            }

            return $this;

        }

    }

	public function files() {
        return $this->morphMany('App\Models\File', 'attachment');
    }

    public function store($selector, $data, $successMsg = 'Saved') {

        //removing files data from data array
        $filesHash = [];
        foreach ($this->fileFields as $fieldKey) {
            $filesHash[$fieldKey] = $data[$fieldKey];
            unset($data[$fieldKey]);
        }

        try {
            $res = $this->updateOrCreate($selector, $data);
        } catch (Exception $e) {
            abort(
                response()->json([ 'status' => 'error', 'res' => $e->getMessage() ], 200)
            );
        }

        $justHashes = [];
        foreach ($filesHash as $field => $fileHash) {

            foreach ($fileHash as $ffhash) {
                if ($ffhash) $justHashes[] = $ffhash;
            }

            //update tmp file to status saved
            File::whereIn('hash',$fileHash)->update([
                'attachment_id'    => $res->hash,
                'attachment_type'  => get_class($this),
                'attachment_field'  => $field,
                'is_saved'         => true
            ]);

        }

        //delete removed files
        File::where('attachment_id',$res->hash)->whereNotIn('hash',$justHashes)->update(['is_saved' => false]);

        if ($successMsg) {

            abort(
                response()->json([
                    'status'    => 'success',
                    'res'       => $successMsg,
                    'obj'       => $res,
                    'hash'      => $res->hash ], 200)
            );

        } else {

            return $res;

        }

    }

}

<?php

namespace App\Models;

use Auth, Field, Uuid;
use App\Models\UserRole;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserRolePermission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $form_fields = [];
    public $validated = false;
    public $form_errors = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'roles_id','sys_access'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getClassName() 
    {
        return substr(__CLASS__, strrpos(__CLASS__, '\\') + 1);
    }


    public static function get_permissions() {

        return UserRolePermission::where('users_roles_id',
                                        self::find(Auth::user()->id)->roles_id
                                    )->get()->pluck('permission','sections_id')->toArray();

    }

    public function scopeList($query)
    {
        return $query->where('roles_id', '>', 1);
    }

    public function role()
    {
       return $this->belongsTo('App\Models\UserRole', 'roles_id');
    }

    public function scopeActive($query)
    {
        return $query->where('sys_access', 1);
    }
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function buildFields($return_fields = false, $field_group = '') {

        $role_options = UserRole::select('id','name')->where('id', '>', 1)->get()->transform(function($item, $key) {
            return [
                'code' => $item['id'],
                'label'  => $item['name']
            ];
        })->toArray();
        $fields = [
            Field::init()->name('name')->label('Name')->required()->toArray(),
            Field::init()->name('email')->label('Email')->required()->toArray(),
            Field::init()->name('password')->type('password')->label('Password')->assignVal(false)->toArray(),
            Field::init()->name('roles_id')->type('select')->label('Access Level')->placeholder('Please select')->options($role_options)->required()->toArray(),
            Field::init()->name('sys_access')->type('echeck')->label(false)->placeholder('System Access')->cssClass('success')->toArray()
        ];

        $this->form_fields = $this->buildFormFields($fields);

        return $return_fields ? $this->form_fields : $this;
    }

    public function getFieldModelValues($preimaryKey = 'hash') {
        $return = [];

        foreach ($this->form_fields as $arrKey => $field) {
            $objKey = $field['db_name'];
            if ($this->$preimaryKey) {
                $return[$objKey] = $field['assignVal'] ? $this->$objKey : '';
            } else {
                $return[$objKey] = $field['dValue'] ? $field['dValue'] : '';
            }
        }

        return $return;
    }

    public static function boot() {
        parent::boot();

        self::saving(function($model) {

            if (!$model->hash) {
				$model->hash = Uuid::generate()->string;
				$model->user_created = app()->runningInConsole() ? '' : Auth::user()->id;
			}
            if (!empty($model->password)) {
                $model->password = bcrypt($model->password);
            } else {
                unset($model->password);
            }
			$model->user_updated = app()->runningInConsole() ? '' : Auth::user()->id;

        });

    }

    public function buildFormFields($fields = []) {
        $arr = [];
        foreach ($fields as $field) {
           $arr[$field['key']] = $field;
        }

        return $arr;
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
                $model_attributes[$field['name']] = $field['label'];
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

    public function store($selector, $data, $successMsg = 'Saved') {
        // dd($data);
        try {
            $this->updateOrCreate($selector, $data);
        } catch (Exception $e) {
            abort(
                response()->json([ 'status' => 'error', 'res' => $e->getMessage() ], 200)
            );
        }

        abort(
            response()->json([ 'status' => 'success', 'res' => $successMsg ], 200)
        );

    }

}

<?php

namespace App\Models;

use App\Models\UserRolePermission;
use Field, Auth, DB, Uuid;
use App\Traits\Helper;
use App\Models\OAD\OADModel;

class UserRole extends OADModel
{
	protected $table = 'users_roles';
	protected $primary = 'id';
	protected $guarded = ['id'];

	public $form_fields = [];
    public $validated = false;
	public $form_errors = [];

	use Helper;

	public function buildFields($with_values = false, $field_group = '') {

        $this->form_fields = $this->buildFormFields([
            Field::init()->name('name')->label('Name')->required()->toArray()
        ]);

        return $this;
    }

	public function scopeList($query)
    {
        return $query->where('id', '>', 1);
	}

	public function items() {
		return $this->hasMany('App\Models\UserRolePermission','users_roles_id');
	}

	public static function boot() {
        parent::boot();

		self::saving(function($model) {

			$items = collect($model->items)->transform(function ($item,$key) {
				return [
					'sections_id'	=> $key,
					'permission'	=> $item
				];
			});

			if (!$model->id) {
				$model->user_created = app()->runningInConsole() ? '' : Auth::user()->id;
			}
			$model->user_updated = app()->runningInConsole() ? '' : Auth::user()->id;

			//saving permissions items
			UserRolePermission::where('users_roles_id',$model->id)->delete();
			$model->items()->createMany($items);
			unset($model->items);
		});

		self::saved(function($model) {
			UserRolePermission::whereNull('users_roles_id')->update(['users_roles_id' => $model->id]);
		});
    }

}

<?php

namespace App\Models\OAD;

use Auth, Uuid;

class File extends OADModel {

    protected $table = 'files';
    protected $guarded = ['hash'];
    protected $primaryKey = 'hash';
    public $incrementing = false;
    public $form_fields = [];
    public $validated = false;
    public $form_errors = [];

    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            $model->hash = Uuid::generate()->string;
            $model->user_updated = app()->runningInConsole() ? '' : Auth::user()->id;
            $model->user_created = app()->runningInConsole() ? '' : Auth::user()->id;
        });

        self::updating(function($model) {
            $model->user_updated = app()->runningInConsole() ? '' : Auth::user()->id;
        });
    }

    public function attachment() {
        return $this->morphTo();
    }

}

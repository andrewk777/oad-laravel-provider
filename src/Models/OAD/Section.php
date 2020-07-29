<?php

namespace App\Models\OAD;

class Section extends OADModel
{
	protected $table = 'sections';

	public function routes() {
        return $this->belongsTo('App\Models\OAD\Router', 'vue_router_id');
    }

}

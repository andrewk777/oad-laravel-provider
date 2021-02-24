
namespace App\Models;

use Field, Uuid;
use App\Models\OAD\OADModel;

class {{ $model_name }} extends OADModel
{
	protected $table = '{{ $migration_table_name }}';
	protected $guarded = ['hash'];
	protected $primaryKey = 'hash';
    public $incrementing = false;


	public $form_fields = [];
    public $validated = false;
    public $form_errors = [];

	public function buildFields($return_fields = false, $field_group = '') {

		@if (count($form_fields))
			$this->form_fields = $this->buildFormFields([
			@foreach ($form_fields as $field)
				Field::init()->name('{{ $field['name'] }}')->type('{{ $field['type'] }}')->label('{{ $field['label'] }}')->toArray(),
			@endforeach
			]);
		@else
		/* $this->form_fields = $this->buildFormFields([
             Field::init()->name('first_name')->label('First Name')->toArray(),
         ]);*/
		@endif

        return $return_fields ? $this->form_fields : $this;
    }

	public function scopeList($query)
    {
        return $query;
    }

    public function scopeExportList($query) {
		return $query->select('hash');
	}

	public static function boot() {
        parent::boot();

        self::creating(function($model) {

			$model->hash = Uuid::generate()->string;

        });

    }


}

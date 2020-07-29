
namespace App\Http\Controllers;

@if ($controller_folder)
use App\Http\Controllers\OADController;
@endif
use Illuminate\Http\Request;
use App\Traits\TableHelpers;

class {{ $controller_name }} extends OADController
{
    use TableHelpers;

    protected $model = 'App\Models\{{ $model_name }}';

    public function index(Request $request)
    {

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model, //class
            config('vars.table.editDelete'),
            //function exnending query
            //filters array of callbacks
            //custom search query
        );

        return response()->json( $response );
    }


    public function show(Request $request)
    {

        $model          = $this->model::find($request->hash) ?? new $this->model;
        $modelsNvalues  = $model->buildFields()->getFieldModelValues();

        return response()->json(
            [
                'status'    => 'success',
                'hash'      => $request->hash,
                'fields'    => $model->form_fields,
                'models'    => $modelsNvalues
            ],
            200
        );
    }

    public function store(Request $request) {

        $model = $this->model::find($request->hash) ?? new $this->model;

        $model->validateForm($request->data)
              ->store([ 'hash' => $request->hash ], $request->data);

    }

}


namespace App\Http\Controllers{{ $folder }};

use App\Http\Controllers\OAD\OADController;
use Illuminate\Http\Request;
use App\Traits\TableHelpers;

class {{ $controller_name }} extends OADController
{
    use TableHelpers;

    protected $model = 'App\Models\{{ $model_name }}';

    public function index(Request $request)
    {

        \User::checkAccess('{{ $section_slug }}',['view','full']);

        //running builder from the trait
        $response = $this->listBuilder(
            new $this->model, //class
            config('project.table.editDelete')
            //function exnending query
            //filters array of callbacks
            //custom search query
        );

        return response()->json( $response );
    }


    public function show(Request $request)
    {

        \User::checkAccess('{{ $section_slug }}',['view','full']);

        $model          = $this->model::find($request->hash) ?? new $this->model;
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

        \User::checkAccess('{{ $section_slug }}','full');

        $model = $this->model::find($request->hash) ?? new $this->model;

        $model->validateForm($request->forms['main']['values'])
              ->store([ 'hash' => $request->hash ], $request->forms['main']['values']);

    }

}

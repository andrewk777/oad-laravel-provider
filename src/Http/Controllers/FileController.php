<?php

namespace OADSOFT\SPA\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller {

    protected $model = 'App\Models\File';

    public function show(Request $request) {

        $model = $request->hash ? $this->model::find($request->hash) : new $this->model;

        $modelsNvalues = $model->buildFields()->getFieldModelValues();

        return response()->json(
                        [
                    'status' => 'success',
                    'hash' => $request->hash,
                    'fields' => $model->form_fields,
                    'models' => $modelsNvalues
                        ], 200
        );
    }

    public function store(Request $request) {

        $return = [];

        if (count($request->all())) {
            foreach ($request->all() as $key => $file) {

                $file_name = $file->getClientOriginalName();

                $model = $this->storeFile($file, [
                    'file_name' => $file_name,
                    'ext' => pathinfo($file_name, PATHINFO_EXTENSION),
                    'size' => $file->getClientSize(),
                    'mime' => $file->getClientMimeType(),
                ]);

                $return[] = [
                    'hash' => $model->hash,
                    'name' => $file_name
                ];
            }
        }

        return response()->json($return);
    }

    public function storeFile($file, $fileInfo = [], $folder = '') {

        $fileInfo['is_saved'] = $folder ? true : false;
        $fileInfo['path'] = $file->store($folder ? $folder : 'default');

        return $this->model::create($fileInfo);
    }

    public function view_file($hash) {

        $file = $this->model::find($hash);
        
        return response()->file(
            storage_path('app/' . $file->path),
            [
                'Content-Type'=>$file->mime ,
                'Content-Disposition' => 'attachment; filename="'. $file->file_name.'"'
            ]);
    }

    public function download_tmp_file($file_name = '') {
        if ($file_name) {
            return response()->file(storage_path('app/tmp/' . $file_name))->deleteFileAfterSend(true);
        }
        return 'No File Found';
    }

}

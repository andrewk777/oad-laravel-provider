<?php

namespace OADSOFT\SPA\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller {

    protected $model = 'App\Models\OAD\File';
    protected $folder = '';

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
            foreach ($request->all() as $file) {

                $file_name = $file->getClientOriginalName();
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                $model = $this->storeFile($file, [
                    'file_name'     => $file_name,
                    'ext'           => $ext,
                    'size'          => $file->getSize(),
                    'mime'          => $file->getClientMimeType(),
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
        $fileInfo['path'] = $file->storeAs($folder ? $folder : 'default', \Str::random(40) . '.' . $fileInfo['ext']);

        return $this->model::create($fileInfo);
    }

    public function view($hash, $file_name = '') {

        $file = $this->model::find($hash);
        $file_name = $file_name ? $file_name : $file->file_name;

        $headers = [
            'Content-Type'          => $file->mime ,
            'Content-Disposition'   => 'filename="'. $file_name.'"'
        ];

        return response()->file(
            storage_path('app/' . $file->path), $headers
        );
        
    }

    public function download($hash, $file_name = '') {

        $file = $this->model::find($hash);
        $file_name = $file_name ? $file_name : $file->file_name;

        $headers = [
            'Content-Type'          => $file->mime ,
            'Content-Disposition'   => 'attachment; filename="'. $file_name.'"'
        ];

        return response()->download(storage_path('app/' . $file->path), $file_name, $headers);
    }

    public function delete(Request $request) {

        $file = $this->model::find($request->hash)->delete();

        //deleting physical file
        \Storage::delete($file->path);

        //setting is saved to 0
        $file->delete();

        return response()->json([ 'status' => 'success', 'res' => 'File Has Been Deleted']);

    }

}

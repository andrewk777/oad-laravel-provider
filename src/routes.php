<?php

//web route - point all requests to this view
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api\/)[\/\w\.-]*');


//api routes listed here
Route::group(
    [
        'prefix'        => 'api', 
        'middleware'    => 'api',
        'namespace'     => 'OADSOFT\SPA'
    ], function() {

        Route::get('auth-check', 'AuthController@auth_check');

});

Route::group(
    [
        'prefix'        => 'api', 
        'middleware'    => ['api','auth:sanctum'],
        'namespace'     => 'OADSOFT\SPA'
    ], function() {

        Route::get('layout', 'LayoutController@index');    
        Route::post('file-upload', 'FileController@store');

});
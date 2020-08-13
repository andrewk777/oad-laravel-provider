<?php

Route::group(
    [
        'middleware'    => 'web',
        'namespace'     => 'App\Http\Controllers\OAD'
    ], function() {

        Route::post('login', [ 'as' => 'login', 'uses' => 'AuthController@login' ]);
        Route::get('logout', [ 'as' => 'logout', 'uses' => 'AuthController@logout' ]);

        //send a request to reset password
        Route::post('resetPasswordSendEmail', 'AuthController@resetPasswordSendEmail' );

        //validate reset token
        Route::get('auth/reset_password_link/{email}/{token}', [
            'as'    => 'password.reset',
            'uses'  =>'AuthController@resetPasswordLink'
        ]);

        //update passwords
        Route::post('resetPasswordComplete', 'AuthController@resetPasswordComplete'); //submit new password
        
});

//api routes listed here
Route::group(
    [
        'prefix'        => 'api', 
        'middleware'    => ['api','auth:sanctum','OADSOFT\SPA\Http\Middleware\OADAuth'],
        'namespace'     => 'App\Http\Controllers\OAD'
    ], function() {
        
        Route::get('layout', 'LayoutController@index');  
        Route::post('file-upload', 'FileController@store');
        Route::get('auth-check', 'AuthController@auth_check');

});


//web route - point all requests to this view
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|sanctum\/)[\/\w\.-]*')->middleware('web'); 


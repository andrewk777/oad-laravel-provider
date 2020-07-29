<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Auth, Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = false;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

     public function logout(Request $request) {
        \Illuminate\Support\Facades\DB::table('personal_access_tokens')->where('tokenable_id',Auth::user()->id)->delete();
        Auth::logout();
        return redirect('/');
     }

    public function login(Request $request) {
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'sys_access' => 1])) {
            $user = Auth::user();
            $token = $user->createToken('api')->plainTextToken;

            return response()->json([ 'status' => 'success', 'res' => 'Logged In', 'token' => $token, 'user' => Auth::user(), 'user' => Auth::check() ], 200);

        } else {

            $res = User::where(['email' => $request->email, 'password' => Hash::make($request->password)])->first();

            $msg = $res && !$res->sys_access 
                    ? 'Access Denied' 
                    : 'Invalid Credentials';

            return response()->json([ 'status' => 'error', 'res' => $msg ], 200);
        }
    }

    public function auth_check(Request $request) {
        return response()->json(['status' => Auth::check()]);
    }
}

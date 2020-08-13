<?php

namespace OADSOFT\SPA\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OAD\UserSession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Auth, Hash, DB;

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

    public function login(Request $request) {

        $user = \User::select('id','hash','name','email','password','sys_access')->where('email',$request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json([ 'status' => 'error', 'res' => 'Invalid Credentials' ], 250);

        } else if ($user->sys_access != 1) {

            return response()->json([ 'status' => 'error', 'res' => 'Access Denied' ], 250);

        }


        //delete all other device sessions for this user
        $user->tokens()->where('tokenable_id',$user->id)->delete();

        auth()->login($user);
        $session = $user->createToken('device-session');

        return response()->json([ 
                'status'        => 'success',
                'res'           => 'Signed In',
                'token'         => $session->plainTextToken,
                'user'          => collect($user)->except(['id','sys_access']) 
            ], 
            200);
        
    }

    public function logout(Request $request) {

        if (Auth::check()) {

            $model = \Laravel\Sanctum\Sanctum::$personalAccessTokenModel;
            $tokenSession = $model::findToken( $request->bearerToken() );

            if ($tokenSession) $tokenSession->delete();

            Auth::logout();

        }
       
       return response()->json([ 'status' => 'success' ], 200);
    }

    public function resetPasswordSendEmail(Request $request) {

        if ($user = \User::list()->active()->where('email',$request->email)->first()) {

            $token = Str::random(40);
            
            DB::table(config('auth.passwords.users.table'))->insert([
                'email'         => $request->email,
                'token'         => $token,
                'created_at'    => now()
            ]);

            $user->notify(new ResetPasswordNotification($token));

            return response()->json([ 'status' => 'success', 'res' => 'Password Reset Email Has Been Sent' ], 200);

        } else {

            return response()->json([ 'status' => 'error', 'res' => 'Email is not recognized' ], 250);

        }

    }

    public function resetPasswordLink(Request $request) {

        if (DB::table(config('auth.passwords.users.table'))->where([
                ['email',$request->email],
                ['token',$request->token],
                ['created_at', '>=' ,\Carbon::now()->subMinutes(60)]
            ])->latest()->first()) {

                return view('app');

        } else {

            return redirect('/auth/reset_password_link/expired');

        }

        

    }

    public function resetPasswordComplete(Request $request) {

        $validator = \Validator::make(
                $request->only('email','token','password','password_confirm'), 
                [
                    'email'             => 'required',
                    'token'             => 'required',
                    'password_confirm'  => 'required|same:password',
                    'password'          => config('project.password_rules')
                ],
                [],
                [
                    'password'          => 'Password',
                    'password_confirm'  => 'Confirm Password'
                ]

            );

            if ( $validator->fails() ) {
                return response()->json(['status' => 'error', 'res' => implode('<br>',$validator->errors()->all()) ], 200);
            }

            \User::where('email',$request->email)->update(['password' => \Hash::make($request->password)]);

            return response()->json(['status' => 'success', 'res' => 'Password Updated'], 200);
    }


    public function auth_check() {

        return response()->json(['status' => auth()->user()->currentAccessToken()->id]);

    }
    
}

<?php

namespace OADSOFT\SPA\Http\Middleware;

use Closure;

class OADAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $model = \Laravel\Sanctum\Sanctum::$personalAccessTokenModel;

        $accessToken = $model::findToken( $request->bearerToken() );

        if (!$accessToken) {
            return response(null, 401);
        }
        
        return $next($request);
    }
}

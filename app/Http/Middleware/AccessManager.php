<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AccessManager
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
        if(Auth::user()->hasAnyRole('manager'))
        {
            return $next($request);
        }

        return $next($request)->with('warning','You do not have Manager Permission');
    }
}

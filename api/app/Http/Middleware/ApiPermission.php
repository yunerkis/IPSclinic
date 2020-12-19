<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class ApiPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (Auth::user()->can($permission)) {

            return $next($request);
        }
        
        return response()->json(['success' => false, 'errors' => 'Forbidden'], 403);
    }
}

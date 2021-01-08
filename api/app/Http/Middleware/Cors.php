<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $handle = $next($request);

        if ($handle->isMethod('OPTIONS')){
            
            $handle = Response::make();
        } 

        if(method_exists($handle, 'header'))
        {   
            $handle->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization, Application, Origin')
            ->header('Content-Type', '*')
            ->header('Accept', 'application/json');
        } else {

            $handle->headers->set('Access-Control-Allow-Origin' , '*');
        }

        return $handle;
    }
}

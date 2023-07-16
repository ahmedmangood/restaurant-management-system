<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        if(auth()->guard('customers')->user())
        {
            return $next($request);
        }
        else{
            if(in_array(auth()->user()['role'],$roles)){
                return $next($request);
            }
            return response()->json(["message"=>"Not Authorize"]);
        }




    }

}

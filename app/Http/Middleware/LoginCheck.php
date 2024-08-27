<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        if(request()->cookie('login') != 'yes') {
            
            return redirect()->route('login');

        }

        return $next($request);

    }
}

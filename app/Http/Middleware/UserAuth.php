<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class UserAuth
{
    public function handle($request, Closure $next)
    {
        if (!Session::has('user_id')) {
            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
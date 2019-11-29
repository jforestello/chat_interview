<?php

namespace App\middlewares;

use Closure;

class Logged {

    public function handle($request, Closure $next)
    {
        if (! isset($_SESSION['user'])) {
            return redirect('/');
        }

        return $next($request);
    }
}
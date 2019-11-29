<?php

namespace App\middlewares;

use Closure;

class Guest {

    public function handle($request, Closure $next)
    {
        if (isset($_SESSION['user'])) {
            return redirect('list');
        }

        return $next($request);
    }
}
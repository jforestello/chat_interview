<?php

namespace App\middlewares;

use Closure;

class StartSession {

    public function handle($request, Closure $next) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return $next($request);
    }
}
<?php

use \Illuminate\Routing\Redirector;
/* @var $redirect Redirector */

if (! function_exists('redirect')) {
    /**
     * Receives a Route name and cause a redirection
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    function redirect(string $route) {
        global $redirect;
        return $redirect->to($route);
    }
}
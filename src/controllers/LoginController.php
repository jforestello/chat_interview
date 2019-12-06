<?php

namespace App\controllers;

class LoginController extends Controller
{
    public function login()
    {
        return $this->parseView('guest/login.html.twig');
    }

    public function register()
    {
        return $this->parseView('guest/register.html.twig');
    }
}

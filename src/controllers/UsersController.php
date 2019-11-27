<?php

namespace App\controllers;

use App\repositories\UserRepository;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(UserRepository $repository)
    {
        return $this->parseView('users/list.html');
    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        return "creating new user named $name";
    }
}

<?php

namespace App\controllers;

use App\models\User;
use App\repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class UsersController extends Controller
{
    public function login(Request $request, UserRepository $repository)
    {
        $input = $request->request->all();
        $errors = [];

        if (empty(trim($input['email']))) {
            $errors[] = "You have to enter an email";
        }
        if (empty(trim($input['pass']))) {
            $errors[] = "You have to enter a password";
        }
        /* @var $user User */
        $user = $repository->fetchOne([
            'email' => $input['email'],
            'password' => hash('sha512', $input['pass'])
        ]);

        if (is_null($user)) {
            $errors[] = "The user or password entered was incorrect";
        }

        if (count($errors)) {
            return $this->parseView('guest/login.html.twig', compact('errors'));
        }

        $_SESSION['user'] = $user;
        return redirect('/list');
    }

    public function register(Request $request, UserRepository $repository)
    {
        $input = $request->request->all();
        $errors = [];

        if (empty(trim($input['name']))) {
            $errors[] = "You have to enter your first name";
        }
        if (empty(trim($input['surname']))) {
            $errors[] = "You have to enter your surname";
        }
        if (empty(trim($input['email']))) {
            $errors[] = "You have to enter an email";
        }
        if (empty(trim($input['pass']))) {
            $errors[] = "You have to enter a password";
        }

        if (! is_null($repository->fetchOne(['email' => $input['email']]))) {
            $errors[] = "The email you submitted is already registered. Please, try to sign in.";
        }

        if (count($errors)) {
            return $this->parseView('guest/register.html.twig', compact('errors'));
        }

        try {
            $user = (new User())
                ->setFirstName($input['name'])
                ->setLastName($input['surname'])
                ->setEmail($input['email'])
                ->setPassword(hash('sha512', $input['pass']));
            $repository->create($user);
            $success = 'Thanks for joining us! You may sign in to start!';
        } catch (\Exception $e) {
            $errors[] = $e->getCode();
            return $this->parseView('guest/register.html.twig', compact('errors'));
        }

        return $this->parseView('guest/register.html.twig', compact('success'));
    }

    public function logout() {
        unset($_SESSION['user']);
        return redirect('/');
    }
}

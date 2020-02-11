<?php

class AuthController {
    public static function showLoginForm () {
        return view('auth.login');
    }

    public static function login () {
        if (Auth::login($_POST['login'], $_POST['password'])) {
            Router::redirect('/');
        } else {
            Router::back();
        }
    }

    public static function showRegisterForm () {
        return view('auth.register');
    }

    public static function register () {
        if (
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
            $_POST['password'] == $_POST['confirm_password']
        ) {
            if (Auth::register($_POST['username'], $_POST['email'], $_POST['password'],
                [ 'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'] ])) {

                Accounts::insert([
                    'name' => $_POST['firstname'] . '\'s Account',
                    'user_id' => Database::lastInsertId(),
                    'amount' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                Router::redirect('/');
            }
        }
        Router::back();
    }

    public static function logout () {
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Router::redirect('/auth/login');
    }
}

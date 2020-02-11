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
            strlen($_POST['firstname']) >= Users::FIRSTNAME_MIN_LENGTH &&
            strlen($_POST['firstname']) <= Users::FIRSTNAME_MAX_LENGTH &&
            strlen($_POST['lastname']) >= Users::LASTNAME_MIN_LENGTH &&
            strlen($_POST['lastname']) <= Users::LASTNAME_MAX_LENGTH &&
            strlen($_POST['username']) >= Users::USERNAME_MIN_LENGTH &&
            strlen($_POST['username']) <= Users::USERNAME_MAX_LENGTH &&
            strlen($_POST['email']) <= Users::EMAIL_MAX_LENGTH &&
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
            strlen($_POST['password']) >= Users::PASSWORD_MIN_LENGTH &&
            strlen($_POST['password']) <= Users::PASSWORD_MAX_LENGTH &&
            $_POST['password'] == $_POST['confirm_password']
        ) {
            if (Auth::register($_POST['username'], $_POST['email'], $_POST['password'],
                [ 'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'] ])) {

                Accounts::insert([
                    'name' => $_POST['firstname'] . '\'s Account',
                    'user_id' => Auth::id(),
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

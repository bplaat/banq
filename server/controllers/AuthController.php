<?php

class AuthController {
    public static function showLoginForm () {
        return view('auth.login');
    }

    public static function login () {
        Auth::login(request('login'), request('password'));
        Router::redirect('/');
    }

    public static function showRegisterForm () {
        return view('auth.register');
    }

    public static function register () {
        validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_VALIDATION,
            'email' => Users::EMAIL_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION
        ]);

        $user_id = Users::createUser([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
            'role' => Users::ROLE_NORMAL
        ]);

        Auth::createSession($user_id);

        Router::redirect('/');
    }

    public static function logout () {
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Router::redirect('/auth/login');
    }
}

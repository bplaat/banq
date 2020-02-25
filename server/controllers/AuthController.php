<?php

class AuthController {
    // The auth show login page
    public static function showLoginForm () {
        return view('auth.login');
    }

    // The auth login page
    public static function login () {
        Auth::login(request('login'), request('password'));
        Router::redirect('/');
    }

    // The auth register form page
    public static function showRegisterForm () {
        return view('auth.register');
    }

    // The auth register page
    public static function register () {
        // Validate the user input
        validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_VALIDATION,
            'email' => Users::EMAIL_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION,
            'phone_number' => USERS::PHONE_NUMBER_VALIDATION,
            'sex' => USERS::SEX_VALIDATION,
            'birth_date' => USERS::BIRTH_DATE_VALIDATION,
            'address' => USERS::ADDRESS_VALIDATION,
            'postcode' => USERS::POSTCODE_VALIDATION,
            'city' => USERS::CITY_VALIDATION,
            'region' => USERS::REGION_VALIDATION
        ]);

        // Create the user
        $user_id = Users::createUser([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
            'phone_number' => request('phone_number'),
            'sex' => request('sex'),
            'birth_date' => request('birth_date'),
            'address' => request('address'),
            'postcode' => request('postcode'),
            'city' => request('city'),
            'region' => request('region'),
            'role' => Users::ROLE_NORMAL
        ]);

        // Create a new session
        Auth::createSession($user_id);

        // Redirect to the home page
        Router::redirect('/');
    }

    // The auth logout page
    public static function logout () {
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Router::redirect('/auth/login');
    }
}

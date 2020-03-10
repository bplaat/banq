<?php

class ApiAuthController {
    // The api auth login route
    public static function login () {
        $session = Auth::login(request('login'), request('password'));
        if (is_string($session)) {
            // Return a confirmation message
            return [
                'success' => true,
                'message' => 'The user has sucessfull logged in',
                'session' => $session
            ];
        } else {
            // Return a error message
            return [
                'success' => false,
                'message' => 'Incorrect username, email or password'
            ];
        }
    }

    // The api auth register route
    public static function register () {
        // Validate the user input
        api_validate([
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
        $session = Auth::createSession($user_id);

        // Return a confirmation message
        return [
            'message' => 'The user has sucessfull registerd',
            'session' => $session
        ];
    }

    // The api auth logout route
    public static function logout () {
        // Revoke the user session
        Auth::revokeSession(request('session'));

        // Return a confirmation message
        return [
            'success' => true,
            'message' => 'The sesions has been revoked'
        ];
    }

    // The api auth edit details route
    public static function edit_details () {
        // Validate the user input fields
        api_validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_EDIT_VALIDATION,
            'email' => Users::EMAIL_EDIT_VALIDATION,
            'phone_number' => USERS::PHONE_NUMBER_VALIDATION,
            'sex' => USERS::SEX_VALIDATION,
            'birth_date' => USERS::BIRTH_DATE_VALIDATION,
            'address' => USERS::ADDRESS_VALIDATION,
            'postcode' => USERS::POSTCODE_VALIDATION,
            'city' => USERS::CITY_VALIDATION,
            'region' => USERS::REGION_VALIDATION
        ]);

        // Update the users information
        Users::update(Auth::id(), [
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'phone_number' => request('phone_number'),
            'sex' => request('sex'),
            'birth_date' => request('birth_date'),
            'address' => request('address'),
            'postcode' => request('postcode'),
            'city' => request('city'),
            'region' => request('region')
        ]);

        // Return a confirmation message
        return [
            'message' => 'The user details have been updated'
        ];
    }

    // The api auth edit password route
    public static function edit_password () {
        // Validate the users input fields
        api_validate([
            'old_password' => Users::OLD_PASSWORD_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION
        ]);

        // Update the authed users password
        Users::update(Auth::id(), [
            'password' => password_hash(request('password'), PASSWORD_DEFAULT)
        ]);

        // Return a confirmation message
        return [
            'message' => 'The user password has been updated'
        ];
    }

    // The api auth delete route
    public static function delete () {
        // Revoke the active user session
        Auth::revokeSession(request('session'));

        // Delete the user
        Users::deleteUser(Auth::id());

        // Return a confirmation message
        return [
            'message' => 'The user has been deleted'
        ];
    }
}

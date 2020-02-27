<?php

class SettingsController {
    // The show settings form page
    public static function showSettingsForm() {
        Auth::updateSession();
        $active_sessions = Sessions::activeSelectPageByUser(Auth::id())->fetchAll();
        echo view('auth.settings', [ 'active_sessions' => $active_sessions ]);
    }

    // The settings change details page
    public static function changeDetails () {
        // Validate the user input fields
        validate([
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

        // Flash a nice message
        Session::flash('messages', [
            'Your user details have changed'
        ]);

        // Redirect to the settings page
        Router::redirect('/auth/settings');
    }

    // The settings change password page
    public static function changePassword () {
        // Validate the users input fields
        validate([
            'old_password' => Users::OLD_PASSWORD_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION
        ]);

        // Update the authed users password
        Users::update(Auth::id(), [
            'password' => password_hash(request('password'), PASSWORD_DEFAULT)
        ]);

        // Flash a nice message
        Session::flash('messages', [
            'Your password has changed'
        ]);

        // Redirect to the settings page
        Router::redirect('/auth/settings');
    }

    // The settings revoke session page
    public static function revokeSession ($session) {
        // Check if the session is from the authed user
        if ($session->user_id == Auth::id()) {
            // Revoke the session
            Auth::revokeSession($session->session);

            // Flash a nice message
            Session::flash('messages', [
                'You have revoked a session'
            ]);

            // Redirect to the settings page
            Router::redirect('/auth/settings');
        } else {
            // Return a 404 page
            return false;
        }
    }

    // The settings delete user page
    public static function deleteUser () {
        // Revoke the active user session
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);

        // Delete the user
        Users::deleteUser(Auth::id());

        // Redirect to the home page
        Router::redirect('/');
    }
}

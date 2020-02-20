<?php

class SettingsController {
    public static function showSettingsForm() {
        Auth::updateSession();
        $active_sessions = Sessions::selectAllActiveByUser(Auth::id())->fetchAll();
        echo view('auth.settings', [ 'active_sessions' => $active_sessions ]);
    }

    public static function changeDetails () {
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

        Session::flash('messages', [
            'Your user details have changed'
        ]);

        Router::redirect('/auth/settings');
    }

    public static function changePassword () {
        validate([
            'old_password' => Users::OLD_PASSWORD_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION
        ]);

        Users::update(Auth::id(), [
            'password' => password_hash(request('password'), PASSWORD_DEFAULT)
        ]);

        Session::flash('messages', [
            'Your password has changed'
        ]);

        Router::redirect('/auth/settings');
    }

    public static function revokeSession ($session) {
        if ($session->user_id == Auth::id()) {
            Auth::revokeSession($session->session);

            Session::flash('messages', [
                'You have revoked a session'
            ]);

            Router::redirect('/auth/settings');
        } else {
            return false;
        }
    }

    public static function deleteUser () {
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Users::deleteUser(Auth::id());
        Router::redirect('/');
    }
}

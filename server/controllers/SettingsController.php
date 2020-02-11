<?php

class SettingsController {
    public static function showSettingsForm() {
        $sessions = Sessions::select([ 'user_id' => Auth::id() ])->fetchAll();
        $active_sessions = [];
        foreach ($sessions as $session) {
            if (strtotime($session->expires_at) > time()) {
                $active_sessions[] = $session;
            }
        }
        echo view('auth.settings', [ 'active_sessions' => $active_sessions ]);
    }

    public static function changeDetails () {
        if (
            strlen($_POST['firstname']) >= Users::FIRSTNAME_MIN_LENGTH &&
            strlen($_POST['firstname']) <= Users::FIRSTNAME_MAX_LENGTH &&
            strlen($_POST['lastname']) >= Users::LASTNAME_MIN_LENGTH &&
            strlen($_POST['lastname']) <= Users::LASTNAME_MAX_LENGTH &&
            strlen($_POST['username']) >= Users::USERNAME_MIN_LENGTH &&
            strlen($_POST['username']) <= Users::USERNAME_MAX_LENGTH &&
            strlen($_POST['email']) <= Users::EMAIL_MAX_LENGTH &&
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
        ) {
            Users::update(Auth::id(), [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'username' => $_POST['username'],
                'email' => $_POST['email']
            ]);
            Router::redirect('/auth/settings');
        }
        Router::back();
    }

    public static function changePassword () {
        if (
            password_verify($_POST['old_password'], Auth::user()->password) &&
            strlen($_POST['password']) >= Users::PASSWORD_MIN_LENGTH &&
            strlen($_POST['password']) <= Users::PASSWORD_MAX_LENGTH &&
            $_POST['new_password'] == $_POST['confirm_new_password']
        ) {
            Users::update(Auth::id(), [
                'password' => password_hash($_POST['new_password'], PASSWORD_DEFAULT)
            ]);
            Router::redirect('/auth/settings');
        }
        Router::back();
    }

    public static function revokeSession ($session) {
        if ($session->user_id == Auth::id()) {
            Auth::revokeSession($session->session);
            Router::redirect('/auth/settings');
        }
        Router::back();
    }

    public static function deleteAccount () {
        Accounts::delete([ 'user_id' => Auth::id() ]);
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Sessions::delete([ 'user_id' => Auth::id() ]);
        Users::delete(Auth::id());
        Router::redirect('/');
    }
}

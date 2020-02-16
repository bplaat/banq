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
        validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_EDIT_VALIDATION,
            'email' => Users::EMAIL_EDIT_VALIDATION
        ]);

        Users::update(Auth::id(), [
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email')
        ]);
        Router::redirect('/auth/settings');
    }

    public static function changePassword () {
        validate([
            'old_password' => 'Users::VERIFY_PASSWORD_VALIDATION',
            'password' => Users::PASSWORD_VALIDATION
        ]);

        Users::update(Auth::id(), [
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ]);
        Router::redirect('/auth/settings');
    }

    public static function revokeSession ($session) {
        if ($session->user_id == Auth::id()) {
            Auth::revokeSession($session->session);
            Router::redirect('/auth/settings');
        }
        Router::back();
    }

    public static function deleteUser () {
        Auth::revokeSession($_COOKIE[SESSION_COOKIE_NAME]);
        Users::deleteUser(Auth::id());
        Router::redirect('/');
    }
}

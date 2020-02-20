<?php

class Auth {
    protected static $checked = false, $user;

    protected static function generateSession () {
        $session = bin2hex(random_bytes(16));
        if (Sessions::select($session)->rowCount() == 1) {
            return static::generateSession();
        }
        return $session;
    }

    public static function createSession ($user_id) {
        static::$checked = false;
        $session = static::generateSession();
        $user_agent = parse_user_agent();
        Sessions::insert([
            'session' => $session,
            'user_id' => $user_id,
            'ip' => get_ip(),
            'browser' => $user_agent['browser'],
            'version' => $user_agent['version'],
            'platform' => $user_agent['platform'],
            'expires_at' => date('Y-m-d H:i:s', time() + SESSION_DURATION)
        ]);
        $_COOKIE[SESSION_COOKIE_NAME] = $session;
        setcookie(SESSION_COOKIE_NAME, $session, time() + SESSION_DURATION, '/', $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
    }

    public static function updateSession () {
        $user_agent = parse_user_agent();
        Sessions::update($_COOKIE[SESSION_COOKIE_NAME], [
            'ip' => get_ip(),
            'browser' => $user_agent['browser'],
            'version' => $user_agent['version'],
            'platform' => $user_agent['platform'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function revokeSession ($session) {
        static::$checked = false;
        Sessions::update($session, [ 'expires_at' => date('Y-m-d H:i:s') ]);
        if ($_COOKIE[SESSION_COOKIE_NAME] == $session) {
            unset($_COOKIE[SESSION_COOKIE_NAME]);
            setcookie(SESSION_COOKIE_NAME, '', time() - 3600, '/', $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
        }
    }

    public static function login ($login, $password) {
        $user_query = Users::selectByLogin($login, $login);
        if ($user_query->rowCount() == 1) {
            $user = $user_query->fetch();
            if (password_verify($password, $user->password)) {
                static::createSession($user->id);
                return;
            }
        }

        Session::flash('errors', [
            'Incorrect username, email or password'
        ]);
        Router::back();
    }

    public static function user () {
        if (!static::$checked) {
            static::$checked = true;
            if (isset($_COOKIE[SESSION_COOKIE_NAME])) {
                $session_query = Sessions::select($_COOKIE[SESSION_COOKIE_NAME]);
                if ($session_query->rowCount() == 1) {
                    $session = $session_query->fetch();
                    if (strtotime($session->expires_at) > time()) {
                        static::$user = Users::select($session->user_id)->fetch();
                        if (strtotime($session->updated_at) + SESSION_UPDATE_DURATION < time()) {
                            Auth::updateSession();
                        }
                    } else {
                        static::revokeSession($session->session);
                    }
                }
            }
        }
        return static::$user;
    }

    public static function check () {
        return static::user() != null;
    }

    public static function id () {
        return static::user()->id;
    }
}

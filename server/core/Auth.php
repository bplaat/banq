<?php

class Auth {
    protected static function generateSession () {
        $session = md5(microtime(true) . $_SERVER['REMOTE_ADDR']);
        if (Sessions::select($session)->rowCount() == 1) {
            return static::generateSession();
        }
        return $session;
    }

    protected static function createSession ($user_id) {
        $session = static::generateSession();
        Sessions::insert([
            'session' => $session,
            'user_id' => $user_id,
            'expires_at' => date('Y-m-d H:i:s', time() + SESSION_DURATION)
        ]);
        $_COOKIE[SESSION_COOKIE_NAME] = $session;
        setcookie(SESSION_COOKIE_NAME, $session, [
            'expires' => time() + SESSION_DURATION,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public static function revokeSession ($session) {
        Sessions::update($session, [ 'expires_at' => date('Y-m-d H:i:s') ]);
        if ($_COOKIE[SESSION_COOKIE_NAME] == $session) {
            unset($_COOKIE[SESSION_COOKIE_NAME]);
            setcookie(SESSION_COOKIE_NAME, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
    }

    public static function login ($login, $password) {
        $user_query = Users::selectByLogin($login, $login);
        if ($user_query->rowCount() == 1) {
            $user = $user_query->fetch();
            if (password_verify($password, $user->password)) {
                static::createSession($user->id);
                return true;
            }
        }
        return false;
    }

    function register ($username, $email, $password, $extra = []) {
        $user_query = Users::selectByLogin($username, $email);
        if ($user_query->rowCount() == 0) {
            Users::insert(array_merge([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ], $extra));
            static::createSession(Database::lastInsertId());
            return true;
        }
        return false;
    }

    protected static $checked = false;
    protected static $user;

    public static function user () {
        if (!static::$checked) {
            static::$checked = true;
            if (isset($_COOKIE[SESSION_COOKIE_NAME])) {
                $session_query = Sessions::select($_COOKIE[SESSION_COOKIE_NAME]);
                if ($session_query->rowCount() == 1) {
                    $session = $session_query->fetch();
                    if (strtotime($session->expires_at) > time()) {
                        static::$user = Users::select($session->user_id)->fetch();
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

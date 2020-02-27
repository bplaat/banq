<?php

class Auth {
    protected static $useCookie = true, $checked = false, $user;

    // A function that sets the use cookie flag
    public static function useCookie($useCookie = null) {
        if (is_null($useCookie)) {
            return static::$useCookie;
        } else {
            static::$useCookie = $useCookie;
        }
    }

    // A function that creates a new session for a user
    public static function createSession ($user_id) {
        static::$checked = false;
        $session = Sessions::generateSession();
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
        if (static::$useCookie) {
            $_COOKIE[SESSION_COOKIE_NAME] = $session;
            setcookie(SESSION_COOKIE_NAME, $session, time() + SESSION_DURATION, '/', $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
        }
        return $session;
    }

    // A function that updates a session with user agent data
    public static function updateSession () {
        $session = static::$useCookie ? $_COOKIE[SESSION_COOKIE_NAME] : request('session');
        $user_agent = parse_user_agent();
        Sessions::update([
            'session' => $session
        ], [
            'ip' => get_ip(),
            'browser' => $user_agent['browser'],
            'version' => $user_agent['version'],
            'platform' => $user_agent['platform'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // A function that revokes a session
    public static function revokeSession ($session) {
        static::$checked = false;
        Sessions::update([
            'session' => $session
        ], [
            'expires_at' => date('Y-m-d H:i:s')
        ]);
        if (static::$useCookie && $_COOKIE[SESSION_COOKIE_NAME] == $session) {
            unset($_COOKIE[SESSION_COOKIE_NAME]);
            setcookie(SESSION_COOKIE_NAME, '', time() - 3600, '/', $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
        }
    }

    // A function that trys to login for a user
    public static function login ($login, $password) {
        $user_query = Users::selectByLogin($login, $login);
        if ($user_query->rowCount() == 1) {
            $user = $user_query->fetch();
            if (password_verify($password, $user->password)) {
                return static::createSession($user->id);
            }
        }

        Session::flash('errors', [
            'Incorrect username, email or password'
        ]);
        Router::back();
    }

    // A function that returns the authed user
    public static function user () {
        if (!static::$checked) {
            static::$checked = true;

            // Check the session cookie for the website
            if (static::$useCookie) {
                if (isset($_COOKIE[SESSION_COOKIE_NAME])) {
                    $session_query = Sessions::select([ 'session' => $_COOKIE[SESSION_COOKIE_NAME] ]);
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

            // Check the session request var for the API
            else {
                if (request('session') != '') {
                    $session_query = Sessions::select([ 'session' => request('session') ]);
                    if ($session_query->rowCount() == 1) {
                        $session = $session_query->fetch();
                        if (strtotime($session->expires_at) > time()) {
                            static::$user = Users::select($session->user_id)->fetch();
                            if (strtotime($session->updated_at) + SESSION_UPDATE_DURATION < time()) {
                                Auth::updateSession();
                            }
                        } else {
                            Sessions::update([
                                'session' => request('session')
                            ], [
                                'expires_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }
        }
        return static::$user;
    }

    // A wrapper function that checks if a user is logged in
    public static function check () {
        return static::user() != null;
    }

    // A wrapper function that fives the id of the authed user
    public static function id () {
        return static::user()->id;
    }
}

<?php

class Session {
    public static $flash;

    public static function init () {
        session_name(SESSION_SHORT_COOKIE_NAME);
        session_start();

        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        static::$flash = [];
        foreach ($_SESSION['_flash'] as $key) {
            static::$flash[$key] = $_SESSION[$key];
            unset($_SESSION[$key]);
        }
        $_SESSION['_flash'] = [];

        // CSRF TOKEN
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['csrf_token'])) {
            Session::flash('errors', [
                'Your did not use the cross-site request forgery token'
            ]);
            Router::back();
        }

        if (isset($_REQUEST['csrf_token'])) {
            if (hash_equals($_REQUEST['csrf_token'], $_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
            } else {
                Session::flash('errors', [
                    'Your cross-site request forgery token is not valid'
                ]);
                Router::back();
            }
        }
    }

    public static function get ($key, $default = '') {
        if (isset(static::$flash[$key])) {
            return static::$flash[$key];
        } elseif (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    public static function set ($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function flash ($key, $value) {
        $_SESSION[$key] = $value;
        $_SESSION['_flash'][] = $key;
    }
}

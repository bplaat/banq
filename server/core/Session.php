<?php

// A wrapper class for the PHP session functionality don't confuse it with the Auth sessions
class Session {
    // The variable to old the old flashed data
    public static $flash;

    // A function which inits the session and does csrf token checks
    public static function init () {
        // Init the PHP session
        session_name(SESSION_SHORT_COOKIE_NAME);
        session_start();

        // Create the flash session var
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        // Move the flash session vars to the flash var
        static::$flash = [];
        foreach ($_SESSION['_flash'] as $key) {
            static::$flash[$key] = $_SESSION[$key];
            unset($_SESSION[$key]);
        }
        $_SESSION['_flash'] = [];

        // CSRF TOKEN

        // Generate a token if no one exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }

        // When a post method dont give a csrf token return with an error
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['csrf_token'])) {
            Session::flash('errors', [
                'Your did not use the cross-site request forgery token'
            ]);
            Router::back();
        }

        // When the token is given check if it is equal
        if (isset($_REQUEST['csrf_token'])) {
            if (hash_equals($_REQUEST['csrf_token'], $_SESSION['csrf_token'])) {
                // Then generate a new one
                $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
            } else {
                // Or return an error to the previous page
                Session::flash('errors', [
                    'Your cross-site request forgery token is not valid'
                ]);
                Router::back();
            }
        }
    }

    // A function to get a session value from the session storage of the flash
    public static function get ($key, $default = '') {
        if (isset(static::$flash[$key])) {
            return static::$flash[$key];
        } elseif (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    // A wrapper function to set a session var
    public static function set ($key, $value) {
        $_SESSION[$key] = $value;
    }

    // A function to flash a value to the session
    public static function flash ($key, $value) {
        $_SESSION[$key] = $value;
        $_SESSION['_flash'][] = $key;
    }
}

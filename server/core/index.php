<?php

// Register the autoloaders
spl_autoload_register(function ($class) {
    $file = ROOT . '/core/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});
spl_autoload_register(function ($class) {
    $file = ROOT . '/controllers/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});
spl_autoload_register(function ($class) {
    $file = ROOT . '/models/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

// Load the config
require_once ROOT . '/config.php';

// Init the PHP session
Session::init();

// Load the other functions
require_once ROOT . '/core/parse_user_agent.php';
require_once ROOT . '/core/validate.php';
require_once ROOT . '/core/view.php';
require_once ROOT . '/core/utils.php';

// When the debug flag is set show all errors
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Connect to the database
Database::connect(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

// When the debug flag is set load the debug routes
if (APP_DEBUG) {
    require_once ROOT . '/core/debug_routes.php';
}

// Load the routes
require_once ROOT . '/routes.php';

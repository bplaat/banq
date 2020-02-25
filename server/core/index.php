<?php

// The directories to be autoloaded
$autoload_folders = [ ROOT . '/core', ROOT . '/models', ROOT . '/controllers' ];
$controller_files = glob(ROOT . '/controllers/*');
foreach ($controller_files as $file) {
    if (is_dir($file)) $autoload_folders[] = $file;
}

// Register the autoloader
spl_autoload_register(function ($class) {
    global $autoload_folders;
    foreach ($autoload_folders as $folder) {
        $path = $folder . '/' . $class . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
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
